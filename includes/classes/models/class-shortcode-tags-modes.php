<?php
/**
 * Plugins Model
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Shortcode_Tags_Modes {
	/**
	 * Get shortcode tags mode list.
	 *
	 * @return array[]
	 */
	public static function all() {
		return array(
			array(
				'value' => 'inherit',
				'text'  => __( 'Inherited from defaults', 'axeptio-wordpress-plugin' ),
			),
			array(
				'value' => 'none',
				'text'  => __( 'None', 'axeptio-wordpress-plugin' ),
			),
			array(
				'value' => 'all',
				'text'  => __( 'All hooks', 'axeptio-wordpress-plugin' ),
			),
			array(
				'value' => 'blacklist',
				'text'  => __( 'Only the following', 'axeptio-wordpress-plugin' ),
			),
			array(
				'value' => 'whitelist',
				'text'  => __( 'Only those other than', 'axeptio-wordpress-plugin' ),
			),
		);
	}
}
