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
				'title'           => 'Cookies WordPress',
				'subTitle'        => "Vous retrouverez ici l'ensemble des extensions utilisant des cookies.",
				'topTitle'        => false,
				'message'         => 'WordPress',
				'image'           => false,
				'imageWidth'      => 0,
				'imageHeight'     => 0,
				'disablePaint'    => false,
				'name'            => '0',
				'layout'          => 'category',
				'allowOptOut'     => true,
				'insert_position' => 'after_welcome_step',
				'position'        => 99,
			),
		);
	}
}
