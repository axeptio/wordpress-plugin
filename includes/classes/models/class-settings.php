<?php
/**
 * Settings Model
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
	 * @param string       $slug          Option slug to retrieve.
	 * @param mixed        $default_value Default value to return if the option is not set.
	 * @param string|false $group   Option group (if false, single value option).
	 * @return bool|mixed|null
	 */
	public static function get_option( string $slug, $default_value = null, $group = 'axeptio_settings' ) {
		$option_group = ! $group ? $slug : $group;

		if ( ! isset( self::$options[ $option_group ] ) ) {
			self::$options[ $option_group ] = get_option( ! $group ? $slug : $group, ! $group ? $default_value : array() );
		}

		if ( $group ) {
			return isset( self::$options[ $group ][ $slug ] ) ? self::$options[ $group ][ $slug ] : $default_value;
		}

		return self::$options[ $option_group ];
	}

	/**
	 * Update option from the database.
	 *
	 * @param string $slug Option value to retrieve.
	 * @param mixed  $value Value to update.
	 * @param string $group Option group (if false, single value option).
	 * @return false|mixed|null
	 */
	public static function update_option( string $slug, $value, string $group = 'axeptio_settings' ) {
		if ( ! $group ) {
			self::$options[ $slug ] = $value;
			return update_option( $slug, $value );
		}

		self::$options = self::get_option( $group, array(), false );

		self::$options[ $group ][ $slug ] = $value;

		return update_option( $group, self::$options[ $group ] );
	}
}
