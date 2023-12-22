<?php

namespace Axeptio\Models;

/**
 * Class handling settings for recommended plugins.
 *
 * This class provides methods to find and process settings for plugins
 * recommended by the Axeptio service.
 */
class Recommended_Plugin_Settings {

	const TRANSIENT_KEY = 'axeptio/recommanded_plugin_settings';

	/**
	 * Stores the results of the fetched plugin data.
	 *
	 * @var array
	 */
	private static $results;

	/**
	 * The URL to fetch plugin data from Axeptio API.
	 *
	 * @var string
	 */
	protected static $service_url = 'https://api.axept.io/v1/vendors/plugin?data.platformId=648c6ff94aabe9248dac7eba';

	/**
	 * Finds settings for a specific plugin.
	 *
	 * @param string $plugin The name of the plugin.
	 * @return array|false The settings for the plugin if found, false otherwise.
	 */
	public static function find( string $plugin ) {
		$result = self::all();
		return isset( $result[ $plugin ] ) ? $result[ $plugin ] : false;
	}

	/**
	 * Retrieves all plugins' settings.
	 *
	 * Fetches plugin data from the API if not already fetched and stored.
	 *
	 * @return array The array of all plugins' settings.
	 */
	public static function all() {
		if ( ! isset( self::$results ) ) {
			self::$results = self::fetch_plugin_datas();
		}
		return self::$results;
	}

	/**
	 * Fetches plugin data from the Axeptio API.
	 *
	 * @return array An array of plugin data.
	 */
	private static function fetch_plugin_datas() {
		$cached_plugin_datas = get_transient(self::TRANSIENT_KEY);

		if ( $cached_plugin_datas !== false ) {
			return $cached_plugin_datas;
		}

		$plugin_datas = wp_remote_get( self::$service_url );

		if ( is_wp_error( $plugin_datas ) ) {
			return array();
		}

		if (! isset( $plugin_datas['body'] ) ) {
			return array();
		}

		$processed_plugin_datas = self::process_plugin_items( json_decode( $plugin_datas['body'], true ) );

		set_transient( 'cached_plugin_datas', $processed_plugin_datas, 1 * DAY_IN_SECONDS );

		return $processed_plugin_datas;
	}

	/**
	 * Processes each plugin item to extract necessary data.
	 *
	 * @param array $plugin_items The array of plugin items to process.
	 * @return array The processed array of plugin data.
	 */
	private static function process_plugin_items( $plugin_items ) {
		$results = array();
		foreach ( $plugin_items as $plugin_item ) {
			if ( self::has_hooks( $plugin_item ) ) {
				$plugin_name = $plugin_item['data']['name'];
				foreach ( $plugin_item['data']['hooks'] as $hook ) {
					self::process_hook( $results, $plugin_name, $hook );
				}
			}
		}
		return $results;
	}

	/**
	 * Checks if a plugin item has hooks.
	 *
	 * @param array $plugin_item The plugin item to check.
	 * @return bool True if hooks exist, false otherwise.
	 */
	private static function has_hooks( $plugin_item ) {
		return isset( $plugin_item['data']['hooks'] ) && count( $plugin_item['data']['hooks'] ) > 0;
	}

	/**
	 * Processes a single hook from the plugin data.
	 *
	 * @param array  $results    The array to store processed data.
	 * @param string $plugin_name The name of the plugin.
	 * @param array  $hook        The hook data to process.
	 */
	private static function process_hook( &$results, $plugin_name, $hook ) {
		if ( 'filter' === $hook['type'] ) {
			$results[ $plugin_name ]['wp_filter_list'][] = $hook['identifier'];
			$results[ $plugin_name ]['wp_filter_mode']   = $results[ $plugin_name ]['wp_filter_mode'] ?? 'blacklist';
		} else {
			$results[ $plugin_name ]['shortcode_tags_list'][] = $hook['identifier'];
			$results[ $plugin_name ]['shortcode_tags_mode']   = $results[ $plugin_name ]['shortcode_tags_mode'] ?? 'blacklist';
		}
	}
}
