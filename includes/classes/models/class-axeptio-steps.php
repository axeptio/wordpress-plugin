<?php
/**
 * Axeptio_Steps class
 *
 * This class provides methods to retrieve information about the Axeptio steps.
 */

namespace Axeptio\Models;

class Axeptio_Steps {

	/**
	 * Get the widget step title
	 *
	 * @return string
	 */
	public static function get_title() {
		$default = esc_html__( 'WordPress Cookies', 'axeptio-wordpress-plugin' );
		return Settings::get_option( 'widget_title', $default ) ?? $default;
	}

	/**
	 * Get the widget step sub-title
	 *
	 * @return string
	 */
	public static function get_sub_title() {
		$default = esc_html__( 'Here you will find all WordPress extensions using cookies.', 'axeptio-wordpress-plugin' );
		return Settings::get_option( 'widget_subtitle', $default ) ?? $default;
	}

	/**
	 * Get the widget step description
	 *
	 * @return string
	 */
	public static function get_description() {
		$default = esc_html__( 'Below is the list of extensions used on this site that utilize cookies. Please activate or deactivate the ones for which you consent to sharing your data.', 'axeptio-wordpress-plugin' );
		return Settings::get_option( 'widget_description', $default ) ?? $default;
	}

	/**
	 * Get all project versions.
	 *
	 * @return array[]
	 */
	public static function all() {
		return array(
			array(
				'title'           => self::get_title(),
				'subTitle'        => self::get_sub_title(),
				'topTitle'        => false,
				'message'         => self::get_description(),
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
