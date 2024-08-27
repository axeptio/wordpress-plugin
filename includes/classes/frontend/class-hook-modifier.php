<?php
/**
 * Hook Modifier
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Frontend;

use Axeptio\Plugin\Models\Plugins;
use Axeptio\Plugin\Models\Recommended_Plugin_Settings;
use Axeptio\Plugin\Models\Sdk;
use Axeptio\Plugin\Models\Settings;
use Axeptio\Plugin\Module;
use Axeptio\Plugin\Utils\Search_Callback_File_Location;
use Axeptio\Plugin\Utils\User_Hook_Parser;
use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

class Hook_Modifier extends Module {


	/**
	 * Stored plugin contents.
	 *
	 * @var array
	 */
	private $plugins_contents = array();

	/**
	 * Plugin errors.
	 *
	 * @var array
	 */
	private $plugins_errors = array();

	const WHITELISTED_HOOK = array(
		'map_meta_cap',
		'user_has_cap',
		'wp_after_insert_post',
		'muplugins_loaded',
		'plugins_loaded',
		'the_permalink_rss',
		'admin_enqueue_scripts',
		'registered_taxonomy',
		'registered_taxonomy_*',
		'registered_post_type',
		'registered_post_type_*',
		'plugin_loaded',
		'wp_roles_init',
		'auth_cookie_valid',
		'set_current_user',
		'sanitize_comment_cookies',
		'setup_theme',
		'unload_textdomain',
		'after_setup_theme',
		'register_setting',
		'wp_sitemaps_init',
		'parse_tax_query',
		'parse_query',
		'pre_get_posts',
		'parse_term_query',
		'pre_get_terms',
		'wp_cache_set_last_changed',
		'posts_selection',
		'debug_bar_enqueue_scripts',
		'wp_loaded',
		'update_option',
		'update_option_*',
		'set_transient_*',
		'setted_transient',
		'requests-*',
		'http_api_curl',
		'http_api_debug',
		'parse_request',
		'send_headers',
		'admin_bar_init',
		'add_admin_bar_menus',
		'loop_start',
		'loop_end',
		'the_post',
		'qm/*',
		'axeptio/*',
		'admin_bar_menu',
		'wp_before_admin_bar_render',
		'wp_after_admin_bar_render',
	);

	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Registering the admin page.
	 *
	 * @return void
	 */
	public function register() {
		if ( ! Sdk::is_active() ) {
			return;
		}

		add_action( 'template_redirect', array( $this, 'on_template_redirect' ) );
		add_action( 'shutdown', array( $this, 'on_shutdown' ) );
	}

	/**
	 * Maybe we should make them private.
	 *
	 * @return void
	 */
	public function on_template_redirect() {
		$this->process_shortcode_tags();
		$this->process_wp_filter();
	}

	/**
	 * Shutdown actions.
	 *
	 * @return void
	 */
	public function on_shutdown() {
		$_SESSION['axeptio_intercepted_content'] = $this->plugins_contents;
	}

	/**
	 * Plugin error handling.
	 *
	 * @param string $plugin Plugin name.
	 * @param string $filter Filter name.
	 * @param string $error Error message.
	 *
	 * @return void
	 */
	public function add_error( string $plugin, string $filter, string $error ) {
		$this->plugins_errors[ $plugin ][] = array(
			'error'  => $error,
			'filter' => $filter,
		);
	}

	/**
	 * Plugin content storage.
	 *
	 * @param string $plugin Plugin name.
	 * @param string $filter Filter name.
	 * @param string $content Content to be stored.
	 *
	 * @return void
	 */
	public function add_content( $plugin, $filter, $content ) {
		$this->plugins_contents[ $plugin ][] = array(
			'content' => $content,
			'filter'  => $filter,
		);
	}

	/**
	 * Process shortcode tags.
	 *
	 * @return array
	 */
	private function process_shortcode_tags() {
		global $shortcode_tags;

		$stats   = array();
		$plugins = array();
		foreach ( $shortcode_tags as $tag => $function ) {
			$stats[ $tag ] = $this->process_function( $function, $tag );
			if ( isset( $stats[ $tag ]['plugin'] ) ) {
				$plugins[ $stats[ $tag ]['plugin'] ][] = array(
					'name'     => $tag,
					'plugin'   => $stats[ $tag ]['plugin'],
					'function' => $function,
				);
			}
		}

		$intercepted_plugins = array();
		// maybe optimize since now $this->plugin_configurations is a map
		// with plugin as keys.
		$cookies_version       = Settings::get_option( 'version', false );
		$cookies_version       = '' === $cookies_version ? 'all' : $cookies_version;
		$plugin_configurations = Plugins::all( $cookies_version );

		foreach ( $plugin_configurations as $plugin_configuration ) {

			$configuration = 'all' === $cookies_version || ! isset( $plugin_configuration['Metas']['Merged'] ) ? $plugin_configuration['Metas'] : $plugin_configuration['Metas']['Merged'];

			// consent has been given for this plugin,
			// no need to add it to the interception.
			if ( ! isset( $configuration['enabled'] ) || ! (bool) $configuration['enabled'] || $this->is_cookie_authorized( $configuration['plugin'] ) ) {
				continue;
			}

			if ( isset( $configuration['shortcode_tags_mode'] ) && 'none' !== $configuration['shortcode_tags_mode'] ) {

				$configuration['shortcode_tags_list'] = 'inherit' === $configuration['shortcode_tags_mode'] ? $plugin_configuration['Metas']['Parent']['shortcode_tags_list'] : $configuration['shortcode_tags_list'];
				$configuration['shortcode_tags_mode'] = 'inherit' === $configuration['shortcode_tags_mode'] ? $plugin_configuration['Metas']['Parent']['shortcode_tags_mode'] : $configuration['shortcode_tags_mode'];

				$configuration = $this->maybe_apply_recommended_settings( $configuration, 'shortcode_tags' );

				// We store the whitelisted tags in the intercepted_plugins array
				// and use the plugin name as key. By doing so, we're able to determine
				// if the plugin should be intercepted AND if there are tags to avoid.
				$intercepted_plugins[ $configuration['plugin'] ] = array(
					'mode'         => $configuration['shortcode_tags_mode'],
					'list'         => explode( "\n", $configuration['shortcode_tags_list'] ),
					'placeholder'  => $configuration['shortcode_tags_placeholder'],
					'vendor_title' => isset( $configuration['vendor_title'] ) && '' !== $configuration['vendor_title'] ? $configuration['vendor_title'] : $plugin_configuration['Name'],
				);
			}
		}

		foreach ( $plugins as $plugin => $tags ) {
			// The plugin has no key in the $intercepted_plugins array,
			// meaning it should not be intercepted.
			if ( ! isset( $intercepted_plugins[ $plugin ] ) ) {
				continue;
			}

			foreach ( $tags as $tag ) {
				if ( $this->should_load_shortcode( $intercepted_plugins[ $plugin ], $tag['name'] ) ) {
					continue;
				}
				$shortcode_tags[ $tag['name'] ] = $this->wrap_tag( $tag['function'], $plugin, $intercepted_plugins[ $plugin ], $tag['name'] ); // PHPCS:Ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		return $stats;
	}

	/**
	 * Should the shortcode be loaded or not?
	 *
	 * @param array  $intercepted_plugin Axeptio plugin settings intercepted.
	 * @param string $name Shortcode tag name.
	 *
	 * @return bool
	 */
	private function should_load_shortcode( $intercepted_plugin, $name ) {
		// If the  name is found in the $intercepted_plugins list
		// and the current mode is whitelist, it should be skipped.

		if ( 'none' === $intercepted_plugin['mode'] ) {
			return true;
		}

		if ( 'whitelist' === $intercepted_plugin['mode'] && in_array( $name, $intercepted_plugin['list'], true ) ) {
			return true;
		}

		// Vice versa, if the name is not found in the $intercepted_plugins list
		// and the current mode is blacklist, it should be skipped as well.

		if ( 'blacklist' === $intercepted_plugin['mode'] && ! in_array( $name, $intercepted_plugin['list'], true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if a hook is whitelisted.
	 *
	 * @param string $hook The hook to check.
	 * @return bool Returns true if the hook is whitelisted, false otherwise.
	 */
	protected function is_whitelisted_hook( string $hook ): bool {
		return array_reduce(
			self::WHITELISTED_HOOK,
			function ( $carry, $whitelisted_hook ) use ( $hook ) {
				if ( true === $carry ) {
					return true;
				}

				if ( strpos( $whitelisted_hook, '*' ) !== false ) {
					$whitelisted_hook = preg_quote( $whitelisted_hook, '/' );
					$pattern          = '/^' . str_replace( '\*', '.*', $whitelisted_hook ) . '$/';
					return (bool) preg_match( $pattern, $hook );
				}

				return $hook === $whitelisted_hook;
			},
			false
		);
	}

	/**
	 * Should the plugin continue to be loaded ?
	 *
	 * @param array $intercepted_plugin Axeptio plugin settings intercepted.
	 * @param mixed $hook Current Hook.
	 * @param mixed $callback Current Callback.
	 * @param mixed $priority Current priority.
	 *
	 * @return bool
	 */
	private function should_load_hook( $intercepted_plugin, $hook, $callback = false, $priority = 10 ) {
		// If the  name is found in the $intercepted_plugins list
		// and the current mode is whitelist, it should be skipped.

		$default = array(
			'hook'     => null,
			'class'    => null,
			'callback' => null,
			'priority' => null,
		);

		$matching_hook = false;

		if ( 'none' === $intercepted_plugin['mode'] ) {
			return true;
		}

		if ( $this->is_whitelisted_hook( $hook ) ) {
			return true;
		}

		foreach ( $intercepted_plugin['list'] as $intercepted_hook ) {
			$current_hook = $default;

			if ( isset( $intercepted_hook['hook'] ) ) {
				$current_hook['hook'] = $hook;
			}

			if ( isset( $intercepted_hook['callback'] ) ) {
				if ( isset( $intercepted_hook['class'] ) && is_array( $callback['function'] ) ) {
					$current_hook['class']    = is_string( $callback['function'][0] ) ? $callback['function'][0] : get_class( $callback['function'][0] );
					$current_hook['callback'] = $callback['function'][1];
				} else {
					$current_hook['callback'] = $callback['function'];
				}
			}

			if ( isset( $intercepted_hook['priority'] ) ) {
				$current_hook['priority'] = $priority;
			}

			if ( $intercepted_hook === $current_hook ) {
				$matching_hook = true;
				break;
			}
		}

		if ( 'whitelist' === $intercepted_plugin['mode'] && $matching_hook ) {
			return true;
		}

		// Vice versa, if the name is not found in the $intercepted_plugins list
		// and the current mode is blacklist, it should be skipped as well.
		if ( 'blacklist' === $intercepted_plugin['mode'] && ! $matching_hook ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if the plugin has been consented or not.
	 *
	 * @param string $plugin Name of the plugin.
	 * @return bool
	 */
	private function is_cookie_authorized( string $plugin ) {
		$cookie = isset( $_COOKIE[ Axeptio_Sdk::OPTION_JSON_COOKIE_NAME ] ) ? json_decode( wp_unslash( $_COOKIE[ Axeptio_Sdk::OPTION_JSON_COOKIE_NAME ] ), JSON_OBJECT_AS_ARRAY ) : array();  // PHPCS:Ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return isset( $cookie[ "wp_{$plugin}" ] ) && true === $cookie[ "wp_{$plugin}" ];
	}

	/**
	 * Maybe set Axeptio recommanded settings if exists.
	 *
	 * @param array $configuration Plugin configuration array.
	 * @param array $setting Name of the settings.
	 * @return array
	 */
	protected function maybe_apply_recommended_settings( $configuration, $setting ) {
		$merged_setting  = $configuration['Merged'][ $setting . '_mode' ] ?? null;
		$current_setting = $configuration[ $setting . '_mode' ] ?? null;

		if ( 'recommended' === $current_setting || 'recommended' === $merged_setting ) {
			$recommended_settings = Recommended_Plugin_Settings::find( $configuration['plugin'] );

			$recommended_mode = $recommended_settings[ $setting . '_mode' ];
			$recommended_list = is_array( $recommended_settings[ $setting . '_list' ] ) ? implode( PHP_EOL, $recommended_settings[ $setting . '_list' ] ) : $recommended_settings[ $setting . '_list' ];

			$configuration[ $setting . '_mode' ] = $recommended_mode;
			$configuration[ $setting . '_list' ] = $recommended_list;

			$configuration['Merged'][ $setting . '_mode' ] = $recommended_mode;
			$configuration['Merged'][ $setting . '_list' ] = $recommended_list;
		}

		return $configuration;
	}



	/**
	 * Wrap the function in a tag
	 *
	 * @return array $stats
	 */
	private function process_wp_filter() {
		/*
		 * WP_filter is a massive array containing all the functions
		 * that will be called when hooks are applied.
		 */
		global $wp_filter;

		$stats   = array();
		$plugins = array();
		foreach ( $wp_filter as $filter => $hook ) {
			foreach ( $hook->callbacks as $priority => $functions ) {
				foreach ( $functions as $name => $function ) {
					$stats[ $filter ][ $name ] = $this->process_function( $function['function'], $name, $filter, $priority );

					if ( isset( $stats[ $filter ][ $name ]['plugin'] ) ) {
						$plugins[ $stats[ $filter ][ $name ]['plugin'] ][] = array(
							'filter'   => $filter,
							'priority' => $priority,
							'name'     => $name,
							'function' => $function,
						);
					}
				}
			}
		}

		$intercepted_plugins = array();

		$cookies_version = Settings::get_option( 'version', 'all' );
		$cookies_version = '' === $cookies_version ? 'all' : $cookies_version;

		$plugin_configurations = Plugins::all( $cookies_version );

		foreach ( $plugin_configurations as $plugin_configuration ) {

			$configuration = 'all' === $cookies_version || ! isset( $plugin_configuration['Metas']['Merged'] ) ? $plugin_configuration['Metas'] : $plugin_configuration['Metas']['Merged'];

			// consent has been given for this plugin,
			// no need to add it to the interception.

			if ( ! isset( $configuration['enabled'] ) || ! (bool) $configuration['enabled'] || $this->is_cookie_authorized( $configuration['plugin'] ) ) {
				continue;
			}

			if ( isset( $configuration['wp_filter_mode'] ) && 'none' !== $configuration['wp_filter_mode'] ) {
				// We store the whitelisted hooks in the intercepted_plugins array
				// and use the plugin name as key. By doing so, we're able to determine
				// if the plugin should be intercepted AND if there hooks to avoid.

				$configuration['wp_filter_list'] = 'inherit' === $configuration['wp_filter_mode'] ? $plugin_configuration['Metas']['Parent']['wp_filter_list'] : $configuration['wp_filter_list'];
				$configuration['wp_filter_mode'] = 'inherit' === $configuration['wp_filter_mode'] ? $plugin_configuration['Metas']['Parent']['wp_filter_mode'] : $configuration['wp_filter_mode'];

				$configuration = $this->maybe_apply_recommended_settings( $configuration, 'wp_filter' );

				$parser = new User_Hook_Parser( $configuration['wp_filter_list'] );
				$hooks  = $parser->get_hooks();

				if ( 0 === count( $hooks ) && ( 'whitelist' === $configuration['wp_filter_mode'] || 'blacklist' === $configuration['wp_filter_mode'] ) ) {
					continue;
				}

				$intercepted_plugins[ $configuration['plugin'] ] = array(
					'mode' => $configuration['wp_filter_mode'],
					'list' => $hooks,
				);
			}
		}

		foreach ( $plugins as $plugin => $configs ) {
			// The plugin has no key in the $intercepted_plugins array,
			// meaning it should not be intercepted.
			if ( ! isset( $intercepted_plugins[ $plugin ] ) ) {
				continue;
			}

			foreach ( $configs as $config ) {
				list($filter, $priority, $name, $function) = array_values( $config );

				if ( $this->should_load_hook( $intercepted_plugins[ $plugin ], $filter, $function, $priority ) ) {
					continue;
				}

				// We decide to prevent admin hooks to be intercepted.
				if ( str_contains( $filter, 'admin' ) ) {
					continue;
				}

				// Otherwise we will wrap and overwrite the filter.
				$wp_filter[ $filter ]->callbacks[ $priority ][ $name ]['function'] = $this->wrap_filter( $function['function'], $plugin, $filter );
			}
		}

		return $stats;
	}

	/**
	 * This method takes a function added to the global $shortcode_tags array
	 * by a plugin and executes it in an output buffer to store its result
	 * for later.
	 *
	 * @param mixed  $callback_function Function to wrap.
	 * @param string $plugin Plugin name.
	 * @param string $plugin_settings Plugin Settings.
	 * @param string $tag Tag name.
	 *
	 * @return Closure
	 */
	private function wrap_tag( $callback_function, $plugin, $plugin_settings, $tag ) {
		return function () use ( $callback_function, $plugin, $plugin_settings, $tag ) {
			$args        = func_get_args();
			$return      = call_user_func_array( $callback_function, $args );
			$pattern     = '/<!--(.*?)-->/s';
			$return      = preg_replace( $pattern, '', $return );
			$placeholder = \Axeptio\Plugin\get_template_part(
				'frontend/shortcode-placeholder',
				array(
					'plugin'          => $plugin,
					'plugin_settings' => $plugin_settings,
				),
				false
			);
			return "$placeholder<!-- axeptio_blocked $plugin \n$return\n-->";
		};
	}


	/**
	 * This method takes a function added to the global $wp_filter array
	 * by a plugin and executes it in an output buffer to store its result
	 * for later.
	 *
	 * @param mixed  $callback_function Callback function to wrap.
	 * @param string $plugin Plugin name.
	 * @param string $filter Filter name.
	 *
	 * @return Closure
	 */
	private function wrap_filter( $callback_function, $plugin, $filter ) {
		return function () use ( $callback_function, $plugin, $filter ) {
			// noop for the moment. ob_start seems to break :
			// "Cannot use output buffering in output buffering display handlers".
		};
	}

	/**
	 * Analyse a callback function and extract information.
	 *
	 * @param mixed $callback_function The callback function to analyze.
	 * @return array|null Information about the callback or null if analysis fails.
	 */
	private function process_function( $callback_function, string $name = null, string $filter = null, $priority = null ) {
		$filename = Search_Callback_File_Location::get_filename( $callback_function, $name, $filter, (int) ($priority ?: 10) );

		if ( ! $filename ) {
			return null;
		}

		return array(
			'filename' => $filename,
			'plugin'   => $this->extract_plugin_name( $filename ),
		);
	}

	/**
	 * Extract the plugin name from a given filename.
	 *
	 * @param string $filename The full path to the file.
	 * @return string|null The plugin name or null if not found.
	 */
	private function extract_plugin_name( $filename ) {
		$plugin_dir = wp_normalize_path( WP_PLUGIN_DIR );
		$filename   = wp_normalize_path( $filename );

		if ( strpos( $filename, $plugin_dir ) === 0 ) {
			$relative_path = substr( $filename, strlen( $plugin_dir ) + 1 );
			$parts         = explode( '/', $relative_path );
			return $parts[0] ?? null;
		}

		return null;
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
	private function getCookiesVersion() {
		return 'not implemented';
	}
}
