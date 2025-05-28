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
	private static function get_default_value(string $field): string {
		return match($field) {
			'widget_title' => esc_html__('WordPress Cookies', 'axeptio-wordpress-plugin'),
			'widget_subtitle' => esc_html__('Here you will find all WordPress extensions using cookies.', 'axeptio-wordpress-plugin'),
			'widget_description' => esc_html__('Below is the list of extensions used on this site that utilize cookies. Please activate or deactivate the ones for which you consent to sharing your data.', 'axeptio-wordpress-plugin'),
			default => '',
		};
	}

	/**
	 * Get widget field value with language support
	 *
	 * @param string $field_name Field base name
	 * @param string $language Language code
	 * @return string Field value
	 */
	private static function get_widget_field(string $field_name, string $language = ''): string {
		$default = self::get_default_value($field_name);

		if ($language) {
			$option_name = $field_name . '_' . $language;
			$value = Settings::get_option($option_name, null);

			// If the value exists, return it
			if ($value !== null) {
				return $value;
			}
		}

		// Fallback to the legacy method (non-language specific)
		return Settings::get_option($field_name, $default) ?? $default;
	}

	/**
	 * Get the widget step title
	 *
	 * @param string $language Language code
	 * @return string
	 */
	public static function get_title(string $language = ''): string {
		return self::get_widget_field('widget_title', $language);
	}

	/**
	 * Get the widget step sub-title
	 *
	 * @param string $language Language code
	 * @return string
	 */
	public static function get_sub_title(string $language = ''): string {
		return self::get_widget_field('widget_subtitle', $language);
	}

	/**
	 * Get the widget step description
	 *
	 * @param string $language Language code
	 * @return string
	 */
	public static function get_description(string $language = ''): string {
		return self::get_widget_field('widget_description', $language);
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
