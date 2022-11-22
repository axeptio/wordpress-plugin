<?php

namespace Axeptio;

use stdClass;

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

	const DEFAULT_WIDGET_CONFIGURATION = [
		'step_title'    => 'Our site uses plugins',
		'step_subTitle' => 'they require your consent as well',
		'step_topTitle' => '',
		'step_message'  => "We're using a platform called Wordpress that's using server-side plugins that can place trackers and scripts. We need your approval for running them."
	];

	static private $_instance = null;

	public $axeptioConfiguration;

	static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	static function getPluginConfigurationsTable() {
		global $wpdb;

		return "{$wpdb->prefix}axeptio_plugin_configuration";
	}


	static function getWidgetConfigurationsTable() {
		global $wpdb;

		return "{$wpdb->prefix}axeptio_widget_configuration";
	}


	public static function activate() {
		global $wpdb;
		$table           = self::getPluginConfigurationsTable();
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
    			`cookie_widget_step` VARCHAR (255) NULL,
	            PRIMARY KEY  (`plugin`, `axeptio_configuration_id`)
		    ) $charset_collate;";

		dbDelta( $sql );


		$table = self::getWidgetConfigurationsTable();
		$sql   = "
			CREATE TABLE IF NOT EXISTS $table (
			    `axeptio_configuration_id` VARCHAR(24) NOT NULL,
			    `step_name` VARCHAR(200) DEFAULT 'wordpress',
			    `insert_position` ENUM('first','after_welcome_step','last') NOT NULL DEFAULT 'after_welcome_step',
			    `position` INT NOT NULL DEFAULT 0,
			    `step_title` TEXT NOT NULL,
			    `step_topTitle` TEXT NULL,
			    `step_subTitle` TEXT NULL,
			    `step_image` VARCHAR(200) NULL,
			    `step_imageHeight` INT NULL,
			    `step_imageWidth` INT NULL,
			    `step_message` TEXT,
			    `step_disablePaint` BOOLEAN NOT NULL DEFAULT 0,
			    PRIMARY KEY  (`axeptio_configuration_id`, `step_name`)
			 ) $charset_collate;
		";

		dbDelta( $sql );

		add_option( self::OPTION_CLIENT_ID, self::DEFAULT_CLIENT_ID );
		add_option( self::OPTION_DB_VERSION, self::DB_VERSION );
		add_option( self::OPTION_JSON_COOKIE_NAME, "axeptio_cookies" );
		add_option( self::OPTION_AUTHORIZED_VENDORS_COOKIE_NAME, "authorized_vendors" );
		add_option( self::OPTION_USER_COOKIES_DURATION, 365 );
	}

	public static function deactivate() {
	}

	public static function getPluginConfigurationURI( $item ) {
		$params = build_query( [
			"page"                     => "axeptio-plugin-configurations",
			"sub"                      => "form",
			"plugin"                   => $item["plugin"],
			"axeptio_configuration_id" => $item["axeptio_configuration_id"],
		] );

		return admin_url( "admin.php?$params" );
	}


	public static function getWidgetConfigurationURI( $item ) {
		$params = build_query( [
			"page"                     => "axeptio-widget-configurations",
			"sub"                      => "form",
			"step_name"                => $item["step_name"],
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
	public static function getPlugin( $plugin ) {
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
		// WordPress recommendation. Doing so, we're able to pass
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
			[ $this, "admin_page" ],
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbDpzcGFjZT0icHJlc2VydmUiIHZpZXdCb3g9IjIxLjg0IDI5Ljc1IDU5LjUzIDM3Ljk4Ij4gICA8cGF0aCBkPSJNMzcuMyA2M2MtLjQuNS0xLjEuNy0xLjguNy0uNiAwLTEuMy0uMy0xLjctLjdMMjIuNiA1MS45Yy0xLS45LTEtMi41LS4xLTMuNXMyLjQtMS4xIDMuNS0uMmMuMSAwIC4xLjEuMS4xbDkuNCA5LjQgMjcuMi0yNy4yYy45LTEgMi41LTEgMy41IDAgLjkgMSAuOSAyLjUgMCAzLjVMMzcuMyA2M3ptMjEuNS04LjRjLTEuMyAwLTIuNC0xLjEtMi40LTIuNCAwLS43LjItMS4zLjctMS43bDE3LjgtMTcuOGMuOS0uOSAyLjQtLjkgMy40IDAgLjkuOS45IDIuNCAwIDMuNEw2MC41IDUzLjljLS40LjQtMSAuNy0xLjcuN004MC43IDYwbC03IDdjLS45LjktMi40IDEtMy40LjEtMS0xLTEtMi41LS4xLTMuNWw3LTdjLjktLjkgMi40LS45IDMuNCAwIDEgMSAxIDIuNS4xIDMuNG0wLTEzLjFMNjcuMSA2MC40Yy0uNC40LTEgLjctMS43LjdzLTEuMi0uMy0xLjctLjdjLS45LS45LS45LTIuNCAwLTMuNGwxMy41LTEzLjZjLjktLjkgMi40LS45IDMuNCAwIDEgMSAxIDIuNi4xIDMuNSIgZmlsbD0iIzIxMjEyMSI+PC9wYXRoPiA8L3N2Zz4K'
		);
		add_submenu_page(
			'axeptio',
			'Widget Configuration',
			'Widget Configuration',
			'manage_options',
			'axeptio-widget-configurations',
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
		if ( isset( $_POST['action'] ) ) {

			if ( $_POST['action'] == 'flush_cache' ) {
				wp_cache_flush_group( 'axeptio' );
			}
			if ( $_POST['action'] == 'settings' ) {

				$current_client_id = get_option( self::OPTION_CLIENT_ID );
				if ( $current_client_id !== esc_attr( $_POST['client_id'] ) ) {
					wp_cache_delete( self::CACHE_AXEPTIO_CONFIGURATION . "_$current_client_id" );
				}

				update_option( self::OPTION_CLIENT_ID, esc_attr( $_POST['client_id'] ) );
				update_option( self::OPTION_COOKIES_VERSION, esc_attr( $_POST['cookies_version'] ) );
				update_option( self::OPTION_TRIGGER_GTM_EVENT, isset( $_POST['trigger_gtm_events'] ) && esc_attr( $_POST['trigger_gtm_events'] ) ? 1 : 0 );
				update_option( self::OPTION_USER_COOKIES_DURATION, esc_attr( $_POST['user_cookies_duration'] ) );
				update_option( self::OPTION_USER_COOKIES_DOMAIN, esc_attr( $_POST['user_cookies_domain'] ) );
				update_option( self::OPTION_USER_COOKIES_SECURE, isset( $_POST['user_cookies_secure'] ) && esc_attr( $_POST['user_cookies_secure'] ) ? 1 : 0 );
				update_option( self::OPTION_AUTHORIZED_VENDORS_COOKIE_NAME, esc_attr( $_POST['authorized_vendors_cookie_name'] ) );
				update_option( self::OPTION_JSON_COOKIE_NAME, esc_attr( $_POST['json_cookie_name'] ) );
			}
		}
		require __DIR__ . "/../wp-admin/settings.php";
	}

	public function widget_configuration_page() {

		global $wpdb;

		if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'form' ) {

			/**
			 * Fetching the GET
			 */
			$row_exists = false;
			if ( isset( $_GET['axeptio_configuration_id'] ) && isset( $_GET['step_name'] ) ) {
				$value = $this->fetchWidgetConfiguration( $_GET['axeptio_configuration_id'], $_GET['step_name'] );
				if ( $value ) {
					$row_exists = true;
				}
			}

			/**
			 * Manage the POST
			 */
			if ( isset( $_POST['action'] ) && $_POST['action'] == 'widget_configuration' ) {

				$payload = [
					"axeptio_configuration_id" => esc_attr( $_POST['cookies_version'] ),
					"step_name"                => esc_attr( $_POST['step_name'] ),
					"step_title"               => esc_attr( $_POST['step_title'] ),
					"step_subTitle"            => esc_attr( $_POST['step_subTitle'] ),
					"step_topTitle"            => esc_attr( $_POST['step_topTitle'] ),
					"step_message"             => stripslashes_deep( wp_kses_post( $_POST['step_message'] ) ),
					"step_image"               => esc_attr( $_POST['step_image'] ),
					"step_imageHeight"         => intval( esc_attr( $_POST['step_imageHeight'] ) ),
					"step_imageWidth"          => intval( esc_attr( $_POST['step_imageWidth'] ) ),
					"step_disablePaint"        => isset( $_POST['step_disablePaint'] ) && esc_attr( $_POST['step_disablePaint'] ) ? 1 : 0,
				];

				if ( ! $row_exists ) {
					$result = $wpdb->insert( self::getWidgetConfigurationsTable(), $payload );
					// Set the new value for the Form
					if ( $result !== false ) {
						$value      = $payload;
						$row_exists = true;
					}
				} else {
					$result = $wpdb->update( self::getWidgetConfigurationsTable(), $payload, [
						'axeptio_configuration_id' => $_GET['axeptio_configuration_id'],
						'step_name'                => $_GET['step_name']
					] );
					if ( $result === 1 ) {
						$value = $payload;
					}
				}
			}

			require __DIR__ . "/../wp-admin/widget-configuration-form.php";

			return;
		}

		require __DIR__ . "/../wp-admin/widget-configurations.php";
	}

	/**
	 *
	 * @return void
	 */
	public function plugin_configurations_page() {

		global $wpdb;

		if ( isset( $_GET['sub'] ) && $_GET['sub'] == 'form' ) {

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
			if ( isset( $_POST['action'] ) && $_POST['action'] == 'plugin_configuration' ) {
				$plugin = Admin::getPlugin( $_POST['plugin'] );
				if ( empty( $plugin ) ) {
					wp_die( "The specified Plugin '$_POST[plugin]' could not be found" );
				}
				$payload = [
					"plugin"                   => $_POST['plugin'],
					"axeptio_configuration_id" => $_POST['cookies_version'],
					"wp_filter_mode"           => isset( $_POST['wp_filter_mode'] ) ? $_POST['wp_filter_mode'] : 'none',
					"wp_filter_list"           => isset( $_POST['wp_filter_list'] ) ? $_POST['wp_filter_list'] : '',
					"shortcode_tags_mode"      => isset( $_POST['shortcode_tags_mode'] ) ? $_POST['shortcode_tags_mode'] : 'none',
					"shortcode_tags_list"      => isset( $_POST['shortcode_tags_list'] ) ? $_POST['shortcode_tags_list'] : '',
					"vendor_title"             => $_POST['vendor_title'] ?: '',
					"vendor_shortDescription"  => $_POST['vendor_shortDescription'] ?: '',
					"vendor_longDescription"   => $_POST['vendor_longDescription'] ?: '',
					"vendor_policyUrl"         => $_POST['vendor_policyUrl'] ?: '',
					"vendor_image"             => $_POST['vendor_image'] ?: '',
					"cookie_widget_step"       => $_POST['cookie_widget_step'] ?: '',
				];

				// Persisting in DB
				$table = self::getPluginConfigurationsTable();
				if ( ! $row_exists ) {
					$result = $wpdb->insert( $table, $payload );
					// Set the new value for the Form
					if ( $result !== false ) {
						$value      = $payload;
						$row_exists = true;
					}
				} else {
					$result = $wpdb->update( $table, $payload, [
						"plugin"                   => $_GET["plugin"],
						"axeptio_configuration_id" => $_GET["axeptio_configuration_id"]
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
	 * @return stdClass[]
	 */
	public function fetchPluginsConfigurations() {
		global $wpdb;
		$table = self::getPluginConfigurationsTable();

		return $wpdb->get_results( "SELECT * FROM $table" );
	}


	private function fetchAxeptioConfiguration() {
		$clientId = get_option( self::OPTION_CLIENT_ID );
		if ( $clientId == self::DEFAULT_CLIENT_ID ) {
			return;
		}
		$key                        = self::CACHE_AXEPTIO_CONFIGURATION . "_$clientId";
		$this->axeptioConfiguration = wp_cache_get( $key );
		if ( ! $this->axeptioConfiguration ) {
			$json    = file_get_contents( "https://client.axept.io/$clientId.json" );
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
		);

		return $wpdb->get_row( $query, ARRAY_A );
	}

	// Make static ?
	public function fetchWidgetConfiguration( $axeptio_configuration_id, $step_name ) {
		global $wpdb;
		$table = self::getWidgetConfigurationsTable();
		$query = $wpdb->prepare(
			"SELECT * FROM $table WHERE `axeptio_configuration_id` = %s AND `step_name` = %s",
			[ $axeptio_configuration_id, $step_name ]
		);

		return $wpdb->get_row( $query, ARRAY_A );
	}

	public function deletePluginConfiguration( $item ) {
		global $wpdb;
		$table = self::getPluginConfigurationsTable();

		return $wpdb->delete( $table, $item );
	}

	public function deleteWidgetConfiguration( $item ) {
		global $wpdb;
		$table = self::getWidgetConfigurationsTable();

		return $wpdb->delete( $table, $item );
	}

	public function fetchWidgetConfigurations( $axeptio_configuration_id = null ) {
		global $wpdb;
		$table = self::getWidgetConfigurationsTable();

		$query = is_null( $axeptio_configuration_id ) ? "SELECT * FROM $table" : $wpdb->prepare(
			"SELECT * FROM $table WHERE `axeptio_configuration_id` = %s",
			[ $axeptio_configuration_id ]
		);

		return $wpdb->get_results( $query, ARRAY_A );
	}


}
