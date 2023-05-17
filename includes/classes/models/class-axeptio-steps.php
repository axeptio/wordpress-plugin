<?php
/**
 * Plugins Model
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Axeptio_Steps {
	/**
	 * Get all project versions.
	 *
	 * @return array[]
	 */
	public static function all() {
		return array(
			array(
				'title'           => esc_html__( 'WordPress Cookies', 'axeptio-wordpress-plugin' ),
				'subTitle'        => esc_html__( 'Here you will find all WordPress extensions using cookies.', 'axeptio-wordpress-plugin' ),
				'topTitle'        => false,
				'message'         => 'WordPress',
				'image'           => false,
				'imageWidth'      => 0,
				'imageHeight'     => 0,
				'disablePaint'    => false,
				'name'            => 'wordpress',
				'layout'          => 'category',
				'allowOptOut'     => true,
				'insert_position' => 'after_welcome_step',
				'position'        => 99,
			),
		);
	}
}
