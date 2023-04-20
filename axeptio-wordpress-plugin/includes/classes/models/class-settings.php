<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Settings {
	/**
	 * Cache options
	 *
	 * @var array Array of settings
	 */
	protected static $options = array();

	/**
	 * Get option from the database.
	 *
	 * @param string $slug          Option slug to retrieve.
	 * @param mixed  $default_value Default value to return if the option is not set.
	 * @return false|mixed|null
	 */
	public static function get_option( string $slug, $default_value = null ) {
		if ( isset( self::$options[ $slug ] ) ) {
			return self::$options[ $slug ];
		}
		return get_option( $slug, $default_value );
	}
}
