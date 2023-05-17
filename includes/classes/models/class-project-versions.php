<?php
/**
 * Plugins Model
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Project_Versions {
	/**
	 * Get all project versions.
	 *
	 * @return array[]
	 */
	public static function all() {
		return json_decode( Settings::get_option( 'xpwp_version_options', '', false ) );
	}
}
