<?php
/**
 * Axeptio_Steps class
 *
 * This class provides methods to retrieve information about the Axeptio steps.
 */

namespace Axeptio\Plugin\Models;

class Axeptio_Steps {

	/**
	 * Get the default values for each widget field
	 *
	 * @param string $field Field name
	 * @return string Default value
	 */
	public static function get_default_value( string $field ): string {
		switch ( $field ) {
			case 'widget_title':
				return __( 'WordPress Cookies', 'axeptio-sdk-integration' );
			case 'widget_subtitle':
				return __( 'Here you will find all WordPress extensions using cookies.', 'axeptio-sdk-integration' );
			case 'widget_description':
				return __( 'Below is the list of extensions used on this site that utilize cookies. Please activate or deactivate the ones for which you consent to sharing your data.', 'axeptio-sdk-integration' );
			default:
				return '';
		}
	}

	/**
	 * Get widget field value with language support
	 *
	 * @param string $field_name Field base name
	 * @param string $language Language code
	 * @return string Field value
	 */
	private static function get_widget_field( string $field_name, string $language = '' ): string {
		$default = self::get_default_value( $field_name );

		if ( ! $language ) {
			return $default;
		}

		$saved_value = Settings::get_option( $field_name . '_' . $language, '' );

		return ! empty( $saved_value ) ? $saved_value : $default;
	}

	/**
	 * Get the widget step title
	 *
	 * @param string $language Language code
	 * @return string
	 */
	public static function get_title( string $language = '' ): string {
		return self::get_widget_field( 'widget_title', $language );
	}

	/**
	 * Get the widget step sub-title
	 *
	 * @param string $language Language code
	 * @return string
	 */
	public static function get_sub_title( string $language = '' ): string {
		return self::get_widget_field( 'widget_subtitle', $language );
	}

	/**
	 * Get the widget step description
	 *
	 * @param string $language Language code
	 * @return string
	 */
	public static function get_description( string $language = '' ): string {
		return self::get_widget_field( 'widget_description', $language );
	}

	/**
	 * Get all project versions.
	 *
	 * @return array[]
	 */
	public static function all() {
		$language = I18n::get_current_language();

		return array(
			array(
				'title'           => self::get_title( $language ),
				'subTitle'        => self::get_sub_title( $language ),
				'topTitle'        => false,
				'message'         => self::get_description( $language ),
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
