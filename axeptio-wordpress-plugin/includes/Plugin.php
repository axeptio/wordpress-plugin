<?php

namespace Axeptio;

use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * The Axeptio Plugin Class is managing the interaction
 * between wordpress plugins and the Axeptio JS SDK
 *
 * It uses a list of Plugins' Configurations that are stored
 * in the Wordpress database to decide which plugin needs to
 * be blocked, intercepted or whitelisted.
 *
 * Two types of entry points are used to block the plugins:
 * 1. The $wp_filter global. It's an array that stores all the
 *    callbacks that will be triggered when Wordpress executes
 *    its hooks. This includes all wp_action, but the wp_enqueue_script
 *    and all their variants.
 * 2. The $shortcode_tags global. It's a list of [custom_tags] that
 *    are declared by plugins developers in order to be written by
 *    the editor in the posts content.
 *
 * This class is also responsible for loading the Axeptio SDK
 *
 * All the global settings are stored in Wordpress options system,
 * including clientId, cookiesVersion, and other.
 *
 * This class loads an additional local JS file (/js/axeptio.js)
 * which handles the addition of a new step in all cookies configurations.
 *
 * @name \Axeptio\Plugin
 * @author Romain Bessuges-Meusy <romain@axeptio.eu>
 */
class Plugin {


	static private ?Plugin $_instance = null;

	static function instance(): Plugin {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private array $_plugins_contents = [];
	private array $_plugins_errors = [];
	private array $_plugin_configurations = [];

	public function __construct() {
		$this->fetchPluginsConfigurations();
		$this->setupHooks();
		$this->setupShortcodes();
	}

	public function fetchPluginsConfigurations() {
		global $wpdb;
		$table = Admin::getPluginConfigurationsTable();
		$rows = $wpdb->get_results( "SELECT * FROM $table" );

		$axeptio_configuration_id = get_option( Admin::OPTION_COOKIES_VERSION );

		// In the eventuality where no configuration has been declared by the admin,
		// we want to determine, based on the published Axeptio configuration
		// which version will be used by the Axeptio SDK when the page is loaded
		if ( empty( $axeptio_configuration_id ) ) {
			$axeptio_configuration_id = $this->getCookiesVersion();
		}

		$this->_plugin_configurations = [];
		foreach ( $rows as $configuration ) {
			// If the config is not scoped to a cookie version, it is applied for the plugin,
			// only if there's no other configuration already defined
			if ( $configuration->axeptio_configuration_id == '' && ! isset( $configurations[ $configuration->plugin ] ) ) {
				$this->_plugin_configurations[ $configuration->plugin ] = $configuration;
			}

			// If the config for the plugin is scoped and matches the current configuration,
			// it is set and overwrites any previously added configuration for the plugin
			if ( $configuration->axeptio_configuration_id == $axeptio_configuration_id ) {
				$this->_plugin_configurations[ $configuration->plugin ] = $configuration;
			}
		}

		// Read cookies to determine if the user accepted the plugin or not already
		if ( ! isset( $_COOKIE[ get_option( Admin::OPTION_JSON_COOKIE_NAME ) ] ) ) {
			return;
		}
		$cookie = json_decode( $_COOKIE[ get_option( Admin::OPTION_JSON_COOKIE_NAME ) ], JSON_OBJECT_AS_ARRAY );
		foreach ( array_keys( $this->_plugin_configurations ) as $plugin ) {
			if ( $cookie["wp_$plugin"] ) {
				$this->_plugin_configurations[ $plugin ]->authorized = true;
			}
		}
	}


	/**
	 * In wordpress plugin development, we need to register our actions
	 * for specific hooks. It is not recommended to just "echo something"
	 */
	private function setupHooks() {
		add_action( "template_redirect", [ $this, "onTemplateRedirect" ] );
		add_action( "shutdown", [ $this, "onShutdown" ] );
		add_action( "wp_enqueue_scripts", [ $this, "addScript" ] );
	}

	private function setupShortcodes() {
		add_shortcode( "axeptio_open_cookie_widget", function ( $atts ) {
			return "<button 
						onclick='openAxeptioCookies()' 
						class='axeptio_open_cookie_widget'>
						$atts[label]
					</button>";
		} );
	}

	/**
	 * This method is called on the wp_enqueue_scripts hook
	 * It's responsible for loading the Axeptio SDK and printing
	 * the correct Axeptio Settings object.
	 *
	 * The clientId, cookiesVersion and other settings are stored
	 * in Wordpress Options table and edited via the Plugin Admin
	 *
	 * @return void
	 */
	public function addScript() {

		// 1. Loading the SDK and preparing the axeptioSettings
		// https://developers.axeptio.eu/sdk/integration-du-sdk#options-and-advanced-mode-declare-an-axeptiosettings-object-in-your-page

		$axeptioSettings = [
			"clientId"                    => get_option( Admin::OPTION_CLIENT_ID ),
			"cookiesVersion"              => get_option( Admin::OPTION_COOKIES_VERSION ),
			"userCookiesDuration"         => intval( get_option( Admin::OPTION_USER_COOKIES_DURATION ) ),
			"triggerGTMEvents"            => ! ! get_option( Admin::OPTION_TRIGGER_GTM_EVENT ),
			"userCookiesDomain"           => get_option( Admin::OPTION_USER_COOKIES_DOMAIN ),
			"userCookiesSecure"           => ! ! get_option( Admin::OPTION_USER_COOKIES_SECURE ),
			"authorizedVendorsCookieName" => get_option( Admin::OPTION_AUTHORIZED_VENDORS_COOKIE_NAME ),
			"jsonCookieName"              => get_option( Admin::OPTION_JSON_COOKIE_NAME ),
			// token ?
		];
		wp_enqueue_script( "axeptio-sdk", "https://static.axept.io/sdk.js" );
		wp_add_inline_script(
			"axeptio-sdk",
			"window.axeptioSettings=" . json_encode( $axeptioSettings, JSON_NUMERIC_CHECK )
		);

		// 2. Loading the local JS for interacting with the SDK (adding the step if needed)
		$wpStep = [
			// need to put all metadata for this step to work
			"title"       => "Wordpress step",
			"subTitle"    => "Subtitle",
			"message"     => "message",
			"layout"      => "category",
			"allowOptOut" => true,
			"vendors"     => array_values( array_map( function ( $pluginConf ) {
				$plugin = Admin::getPlugin($pluginConf->plugin);
				return [
					"name"             => "wp_$pluginConf->plugin",
					"title"            => $pluginConf->vendor_title ?: $plugin['Title'],
					"shortDescription" => $pluginConf->vendor_shortDescription ?: $plugin['Description'],
					"longDescription"  => $pluginConf->vendor_longDescription,
					"policyUrl"        => $pluginConf->vendor_policyUrl ?: $plugin['PluginURI'],
					"image"            => $pluginConf->vendor_image,
					"type"             => "wordpress plugin"
				];
			}, $this->_plugin_configurations ) )
		];
		wp_enqueue_script(
			"axeptio-script",
			plugins_url( '/js/axeptio.js', AXEPTIO_PLUGIN_FILE ),
		);
		wp_localize_script( 'axeptio-script', 'axeptioWordpressStep', $wpStep );
	}

	// maybe we should make them private
	public function onTemplateRedirect() {
		$this->processShortcodeTags();
		$this->processWpFilter();
	}

	public function onShutdown() {
		$_SESSION["axeptio_intercepted_content"] = $this->_plugins_contents;
	}

	public function addError( $plugin, $filter, $error ) {
		$this->_plugins_errors[ $plugin ][] = [
			"error"  => $error,
			"filter" => $filter
		];
	}

	public function addContent( $plugin, $filter, $content ) {
		$this->_plugins_contents[ $plugin ][] = [
			"content" => $content,
			"filter"  => $filter
		];
	}

	private function processShortcodeTags() {

		global $shortcode_tags;

		$stats = [];
		$plugins = [];
		foreach ( $shortcode_tags as $tag => $function ) {
			$stats[ $tag ] = $this->processFunction( $function );
			if ( ! is_null( $stats[ $tag ]["plugin"] ) ) {
				$plugins[ $stats[ $tag ]["plugin"] ][] = [
					"name"     => $tag,
					"plugin"   => $stats[ $tag ]["plugin"],
					"function" => $function
				];
			}
		}

		$intercepted_plugins = [];
		// maybe optimize since now $this->_plugin_configurations is a map
		// with plugin as keys.
		foreach ( $this->_plugin_configurations as $configuration ) {
			// consent has been given for this plugin,
			// no need to add it to the interception
			if ( $configuration->authorized ) {
				continue;
			}

			if ( $configuration->shortcode_tags_mode != 'none' ) {
				// We store the whitelisted tags in the intercepted_plugins array
				// and use the plugin name as key. By doing so, we're able to determine
				// if the plugin should be intercepted AND if there are tags to avoid
				$intercepted_plugins[ $configuration->plugin ] = [
					"mode"        => $configuration->shortcode_tags_mode,
					"list"        => explode( ",", $configuration->shortcode_tags_list ),
					"placeholder" => $configuration->shortcode_tags_placeholder
				];
			}
		}

		foreach ( $plugins as $plugin => $tags ) {
			// The plugin has no key in the $intercepted_plugins array,
			// meaning it should not be intercepted.
			if ( ! isset( $intercepted_plugins[ $plugin ] ) ) {
				continue;
			}

			foreach ( $tags as $tag ) {
				if($this->shouldContinue($intercepted_plugins[ $plugin ], $tag['name']))
					continue;
				$shortcode_tags[ $tag['name'] ] = $this->wrapTag( $tag['function'], $plugin, $tag['name'] );
			}
		}

		return $stats;
	}

	private function shouldContinue($intercepted_plugin, $name):bool{
		// If the  name is found in the $intercepted_plugins list
		// and the current mode is whitelist, it should be skipped.
		if ( $intercepted_plugin["mode"] == 'whitelist'
		     && in_array( $name, $intercepted_plugin["list"] ) ) {
			return true;
		}
		// Vice versa, if the name is not found in the $intercepted_plugins list
		// and the current mode is blacklist, it should be skipped as well.
		if ( $intercepted_plugin["mode"] == 'blacklist'
		     && ! in_array( $name, $intercepted_plugin["list"] ) ) {
			return true;
		}
		return false;
	}

	private function processWpFilter() {
		/*
		 * WP_filter is a massive array containing all the functions
		 * that will be called when hooks are applied.
		 */
		global $wp_filter;

		$stats = [];
		$plugins = [];
		foreach ( $wp_filter as $filter => $hook ) {
			foreach ( $hook->callbacks as $priority => $functions ) {
				foreach ( $functions as $name => $function ) {
					$stats[ $filter ][ $name ] = $this->processFunction( $function['function'] );
					if ( ! is_null( $stats[ $filter ][ $name ]["plugin"] ) ) {
						$plugins[ $stats[ $filter ][ $name ]["plugin"] ][] = [
							"filter"   => $filter,
							"priority" => $priority,
							"name"     => $name,
							"function" => $function
						];
					}
				}
			}
		}

		$intercepted_plugins = [];
		// maybe optimize since now $this->_plugin_configurations is a map
		// with plugin as keys.
		foreach ( $this->_plugin_configurations as $configuration ) {
			// consent has been given for this plugin,
			// no need to add it to the interception
			if ( $configuration->authorized ) {
				continue;
			}
			//
			if ( $configuration->wp_filter_mode != 'none' ) {
				// We store the whitelisted hooks in the intercepted_plugins array
				// and use the plugin name as key. By doing so, we're able to determine
				// if the plugin should be intercepted AND if there hooks to avoid
				$intercepted_plugins[ $configuration->plugin ] = [
					"mode"         => $configuration->wp_filter_mode,
					"list"         => explode( ",", $configuration->wp_filter_list ),
					"store_output" => $configuration->wp_filter_store_output
				];
			}
		}

		foreach ( $plugins as $plugin => $configs ) {
			// The plugin has no key in the $intercepted_plugins array,
			// meaning it should not be intercepted.
			if ( ! isset( $intercepted_plugins[ $plugin ] ) ) {
				continue;
			}
			foreach ( $configs as $config ) {
				extract( $config );

				if($this->shouldContinue($intercepted_plugins[ $plugin ], $filter))
					continue;

				// We decide to prevent admin hooks to be intercepted
				if ( str_contains( $filter, "admin" ) ) {
					continue;
				}
				// Otherwise we will wrap and overwrite the filter.
				// Todo => here we should pass the store_output option
				$wp_filter[ $filter ]->callbacks[ $priority ][ $name ]["function"] = $this->wrapFilter( $function['function'], $plugin, $filter );
			}
		}

		return $stats;
	}

	/**
	 * This method takes a function added to the global $shortcode_tags array
	 * by a plugin and executes it in an output buffer to store its result
	 * for later.
	 *
	 *
	 * @param $function
	 * @param $plugin
	 * @param $filter
	 *
	 * @return Closure
	 */
	private function wrapTag( $function, $plugin, $tag ): Closure {
		return function () use ( $function, $plugin, $tag ) {
			$args = func_get_args();
			$return = call_user_func_array( $function, $args );

			// Todo add a placeholder
			return "<!-- axeptio_blocked $plugin \n{$return}\n-->";
		};
	}


	/**
	 * This method takes a function added to the global $wp_filter array
	 * by a plugin and executes it in an output buffer to store its result
	 * for later.
	 *
	 *
	 * @param $function
	 * @param $plugin
	 * @param $filter
	 *
	 * @return Closure
	 */
	private function wrapFilter( $function, $plugin, $filter ): Closure {
		return function () use ( $function, $plugin, $filter ) {
			$args = func_get_args();
			ob_start();
			try {
				call_user_func_array( $function, $args );
			} catch ( \Error $e ) {
				error_log( $e->getMessage() );
				$this->addError( $plugin, $filter, $e );
			}
			$this->addContent( $plugin, $filter, ob_get_clean() );
		};
	}

	/**
	 * @param $function
	 *
	 * @return array
	 */
	private function processFunction( $function ): array {
		try {
			if ( is_string( $function ) || $function instanceof Closure ) {
				$reflection = new ReflectionFunction( $function );
			} else {
				$reflection = new ReflectionMethod( $function[0], $function[1] );
			}

			$filename = $reflection->getFileName();
			$pluginRegExpMatches = [];
			preg_match( '#wp-content[/\\\]plugins[/\\\]([a-zA-Z0-9_-]+)[/\\\]#', $filename, $pluginRegExpMatches );

			return [
				'filename' => $filename,
				'plugin'   => $pluginRegExpMatches[1]
			];
		} catch ( ReflectionException $e ) {
			return [ 'error' => $e->getMessage() ];
		}

	}

	/**
	 * Fetch the client configuration and determines which cookies version
	 * will be selected by the SDK (reimplements the SDK algorithm)
	 *
	 * @note Maybe take cookies in consideration?
	 * @see https://github.com/axeptio/caas-styleguide/blob/staging/src/sdk/SDK.js#L653-L701
	 * @todo implement
	 * @return string
	 */
	private function getCookiesVersion(): string {
		return "not implemented";
	}
}