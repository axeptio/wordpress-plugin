<?php

namespace Axeptio;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Admin {
	const OPTION_CLIENT_ID = "axeptio_client_id";
	const OPTION_DB_VERSION = "axeptio_db_version";
	const OPTION_COOKIES_VERSION = "axeptio_cookies_version";
	const OPTION_USER_COOKIES_DURATION = "axeptio_settings_userCookiesDuration";
	const OPTION_USER_COOKIES_DOMAIN = "axeptio_settings_userCookiesDomain";
	const OPTION_USER_COOKIES_SECURE = "axeptio_settings_userCookiesSecure";
	const OPTION_AUTHORIZED_VENDORS_COOKIE_NAME = "axeptio_settings_authorizedVendorsCookieName";
	const OPTION_JSON_COOKIE_NAME = "axeptio_settings_jsonCookieName";
	const OPTION_TRIGGER_GTM_EVENT = "axeptio_settings_triggerGTMEvents";

	const DEFAULT_CLIENT_ID = "Replace with your Axeptio Client ID";
	const DB_VERSION = "1.0.0";

	const NONCE_NAME = "axeptio_admin";
	const CACHE_AXEPTIO_CONFIGURATION = "axeptio_client_configuration";

	static private ?Admin $_instance = null;

	public $axeptioConfiguration;

	static function instance(): Admin {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	static function getPluginConfigurationsTable(): string {
		global $wpdb;

		return "{$wpdb->prefix}axeptio_plugin_configuration";
	}

	public static function activate() {
		global $wpdb;
		$table = self::getPluginConfigurationsTable();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table  (
                -- primary key if composed of the plugin name and the axeptio_configuration_id
                -- it would maybe be better to use a unique index and auto_increment column?
	            `plugin` varchar(80) NOT NULL,
	            `axeptio_configuration_id` varchar(24) NOT NULL,
	            -- settings that tell to the Axeptio WP Plugin how to behave with this plugin's filters
	            `wp_filter_mode` ENUM('none', 'all', 'blacklist', 'whitelist') NOT NULL DEFAULT 'all',
	            `wp_filter_list` TEXT NULL,
	            `wp_filter_store_output` BOOLEAN NOT NULL DEFAULT 0,
	            `wp_filter_reload_page_after_consent` ENUM('no', 'ask', 'yes') NOT NULL DEFAULT 'no',
                -- settings that tell to the Axeptio WP Plugin how to behave with this plugin's shortcodes
	            `shortcode_tags_mode` ENUM('none', 'all', 'blacklist', 'whitelist') NOT NULL DEFAULT 'all',
	            `shortcode_tags_list` TEXT NULL,
	            `shortcode_tags_placeholder` TEXT,
	            -- info about that will be displayed in the widget
	            `vendor_id` varchar(24) NULL,
	            `vendor_title` TEXT NOT NULL,
	            `vendor_shortDescription` TEXT NOT NULL,
	            `vendor_longDescription` TEXT NOT NULL,
	            `vendor_policyUrl` TEXT NOT NULL,
	            `vendor_image` TEXT NOT NULL,		               
	            PRIMARY KEY  (`plugin`, `axeptio_configuration_id`)
		    ) $charset_collate;";

		dbDelta( $sql );
		add_option( self::OPTION_CLIENT_ID, self::DEFAULT_CLIENT_ID );
		add_option( self::OPTION_DB_VERSION, self::DB_VERSION );
		add_option( self::OPTION_JSON_COOKIE_NAME, "axeptio_cookies" );
		add_option( self::OPTION_AUTHORIZED_VENDORS_COOKIE_NAME, "authorized_vendors" );
		add_option( self::OPTION_USER_COOKIES_DURATION, 365 );
	}

	public static function deactivate() {
	}

	public static function getPluginConfigurationURI( $item ): string {
		$params = build_query( [
			"page"                     => "axeptio-plugin-configurations",
			"sub"                      => "form",
			"plugin"                   => $item["plugin"],
			"axeptio_configuration_id" => $item["axeptio_configuration_id"],
		] );

		return admin_url( "admin.php?$params" );
	}


	/**
	 * Iterates through the list of plugins
	 * and returns the one where the folder matches
	 * the $plugin param.
	 *
	 * @param $plugin
	 *
	 * @return array|null
	 */
	public static function getPlugin( $plugin ): ?array {
		foreach ( get_plugins() as $path => $data ) {
			$name = ( strpos( $path, '/' ) !== false ) ? dirname( $path ) : basename( $path );
			if ( $name === $plugin ) {
				return $data;
			}
		}

		return null;
	}

	public function __construct() {
		// This is used to add client side script to the admin panel
		// we decide to use the enqueue scripts in order to follow
		// wordpress recommendation. Doing so, we're able to pass
		// a nonce that makes sure we're not forging the request from outside
		add_action( 'admin_enqueue_scripts', [ $this, "enqueue_scripts" ] );
		add_action( 'wp_ajax_plugin_configurations', [ $this, "ajax_handler" ] );

		add_action( 'admin_menu', [ $this, "admin_menu" ] );
		// we fetch the Axeptio JSON for the clientId from the Axeptio CDN or the WP Cache
		$this->fetchAxeptioConfiguration();
	}

	public function ajax_handler() {
		check_ajax_referer( self::NONCE_NAME );
		wp_die(); // All ajax handlers die when finished
	}

	public function enqueue_scripts( $hook ) {
		// NONCE used to identified subsequent AJAX calls
		$axeptio_admin_nonce = wp_create_nonce( self::NONCE_NAME );
		switch ( $hook ) {
			case "axeptio_page_plugin-configurations":
				wp_enqueue_script(
					'plugin_configurations-script',
					plugins_url( '/wp-admin/js/plugin-configurations.js', AXEPTIO_PLUGIN_FILE ),
					[ 'jquery' ],
					'1.0.2',
					true
				);
				wp_localize_script(
					'plugin_configurations-script',
					'axeptio_plugin_configurations',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => $axeptio_admin_nonce,
					)
				);
				break;
			default;
		}
	}

	public function admin_menu() {
		add_menu_page(
			'Axeptio Settings',
			'Axeptio',
			'manage_options',
			'axeptio',
			[ $this, "admin_page" ]
		);
		add_submenu_page(
			'axeptio',
			'Widget Configuration',
			'Widget Configuration',
			'manage_options',
			'axeptio-widget-configuration',
			[ $this, "widget_configuration_page" ]
		);
		add_submenu_page(
			'axeptio',
			'Plugins Configuration',
			'Plugins Configuration',
			'manage_options',
			'axeptio-plugin-configurations',
			[ $this, "plugin_configurations_page" ]
		);
	}

	public function admin_page() {

		if ( $_POST['action'] == 'flush_cache' ) {
			wp_cache_flush_group( 'axeptio' );
		}
		if ( $_POST['action'] == 'settings' ) {

			$current_client_id = get_option( self::OPTION_CLIENT_ID );
			if ( $current_client_id !== $_POST['client_id'] ) {
				wp_cache_delete( self::CACHE_AXEPTIO_CONFIGURATION . "_$current_client_id" );
			}

			update_option( self::OPTION_CLIENT_ID, $_POST['client_id'] );
			update_option( self::OPTION_COOKIES_VERSION, $_POST['cookies_version'] );
			update_option( self::OPTION_TRIGGER_GTM_EVENT, $_POST['trigger_gtm_events'] );
			update_option( self::OPTION_USER_COOKIES_DURATION, $_POST['user_cookies_duration'] );
			update_option( self::OPTION_USER_COOKIES_DOMAIN, $_POST['user_cookies_domain'] );
			update_option( self::OPTION_USER_COOKIES_SECURE, $_POST['user_cookies_secure'] );
			update_option( self::OPTION_AUTHORIZED_VENDORS_COOKIE_NAME, $_POST['authorized_vendors_cookie_name'] );
			update_option( self::OPTION_JSON_COOKIE_NAME, $_POST['json_cookie_name'] );
		}

		require __DIR__ . "/../wp-admin/settings.php";
	}

	public function widget_configuration_page() {
		require __DIR__ . "/../wp-admin/widget-configuration.php";
	}

	/**
	 *
	 * @return void
	 */
	public function plugin_configurations_page() {

		global $wpdb;

		if ( $_GET['sub'] == 'form' ) {

			/**
			 * Fetching the GET
			 */
			$row_exists = false;
			if ( isset( $_GET['plugin'] ) ) {
				$value = $this->fetchPluginConfiguration( $_GET['plugin'], $_GET['axeptio_configuration_id'] );
				if ( $value ) {
					$row_exists = true;
				}
			}

			/**
			 * Manage the POST
			 */
			if ( $_POST['action'] == 'plugin_configuration' ) {
				$plugin = Admin::getPlugin( $_POST['plugin'] );
				if ( empty( $plugin ) ) {
					wp_die( "The specified Plugin '$_POST[plugin]' could not be found" );
				}
				$payload = [
					"plugin"                   => $_POST['plugin'],
					"axeptio_configuration_id" => $_POST['cookies_version'],
					"wp_filter_mode"           => $_POST['wp_filter_mode'] ?? 'none',
					"wp_filter_list"           => $_POST['wp_filter_list'] ?? '',
					"shortcode_tags_mode"      => $_POST['shortcode_tags_mode'] ?? 'none',
					"shortcode_tags_list"      => $_POST['shortcode_tags_list'] ?? '',
					"vendor_title"             => $_POST['vendor_title'] ?: '',
					"vendor_shortDescription"  => $_POST['vendor_shortDescription'] ?: '',
					"vendor_longDescription"   => $_POST['vendor_longDescription'] ?: '',
					"vendor_policyUrl"         => $_POST['vendor_policyUrl'] ?: '',
					"vendor_image"             => $_POST['vendor_image'] ?: '',
				];

				// Persisting in DB
				$table = self::getPluginConfigurationsTable();
				if ( ! $row_exists ) {
					$result = $wpdb->insert( $table, $payload );
					$params = implode( '&', [
						"page"                     => "axeptio-plugin-configurations",
						"sub"                      => "form",
						"plugin"                   => $payload["plugin"],
						"axeptio_configuration_id" => $payload["axeptio_configuration_id"],
					] );

					// Set the new value for the Form
					if ( $result !== false ) {
						$value = $payload;
						$row_exists = true;
					}
				} else {
					$result = $wpdb->update( $table, $payload, [
						"plugin"                   => $payload["plugin"],
						"axeptio_configuration_id" => $payload["axeptio_configuration_id"]
					] );
					if ( $result !== false ) {
						// Set the new value for the Form
						$value = $payload;
					}
				}
			}

			// We render the form
			require __DIR__ . "/../wp-admin/plugin-configuration-form.php";

			return;
		}

		// We render the list
		require __DIR__ . "/../wp-admin/plugin-configurations.php";
	}

	/**
	 * @return \stdClass[]
	 */
	public function fetchPluginsConfigurations(): array {
		global $wpdb;
		$table = self::getPluginConfigurationsTable();

		return $wpdb->get_results( "SELECT * FROM $table" );
	}


	private function fetchAxeptioConfiguration() {
		$clientId = get_option( self::OPTION_CLIENT_ID );
		if ( $clientId == self::DEFAULT_CLIENT_ID ) {
			return;
		}
		$key = self::CACHE_AXEPTIO_CONFIGURATION . "_$clientId";
		$this->axeptioConfiguration = wp_cache_get( $key );
		if ( ! $this->axeptioConfiguration ) {
			$json = file_get_contents( "https://client.axept.io/$clientId.json" );
			$decoded = json_decode( $json );
			wp_cache_add( $key, $decoded, "axeptio", 60 * 60 * 24 );
			$this->axeptioConfiguration = $decoded;
		}
	}

	public function getCookiesConfig( $cookiesVersion ) {
		foreach ( $this->axeptioConfiguration->cookies as $cookie ) {
			if ( $cookie->identifier == $cookiesVersion ) {
				return $cookie;
			}
		}

		return null;
	}

	// Make static ?
	public function fetchPluginConfiguration( $plugin, $axeptio_configuration_id ) {
		global $wpdb;
		$table = self::getPluginConfigurationsTable();
		$query = $wpdb->prepare(
			"SELECT * FROM $table WHERE `plugin` = %s AND `axeptio_configuration_id` = %s",
			[ $plugin, $axeptio_configuration_id ]
		);;

		return $wpdb->get_row( $query, ARRAY_A );
	}

	public function deletePluginConfiguration( $item ) {
		global $wpdb;
		$table = self::getPluginConfigurationsTable();
		return $wpdb->delete( $table, $item );
	}


}