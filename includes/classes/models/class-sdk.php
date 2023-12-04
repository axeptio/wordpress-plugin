<?php
/**
 * Plugins Model
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Sdk {
	/**
	 * Check if SDK is active.
	 *
	 * @return bool
	 */
	public static function is_active(): bool {
		return (bool) Settings::get_option( 'sdk_active', false );
	}
}
