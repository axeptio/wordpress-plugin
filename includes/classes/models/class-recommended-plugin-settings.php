<?php
/**
 * Settings Model
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Recommended_Plugin_Settings {
	/**
	 * Define static property to store find() results.
	 *
	 * @var array Array of results
	 */
	private static $results = array();

	/**
	 * Search for recommended plugin settings.
	 *
	 * @param string $plugin Plugin name.
	 * @return false|array
	 */
	public static function find( string $plugin ) {
		// First check if results have already been stored.
		if ( isset( self::$results[ $plugin ] ) ) {
			return self::$results[ $plugin ];
		}

		$plugin_settings = XPWP_INC . DS . 'vendor-settings' . DS . $plugin . '.php';
		if ( file_exists( $plugin_settings ) ) {
			$settings = include $plugin_settings;
			// Store results in static property.
			self::$results[ $plugin ] = $settings;
			return $settings;
		}
		return false;
	}
}
