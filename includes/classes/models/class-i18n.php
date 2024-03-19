<?php
/**
 * Class i18n
 *
 * This class provides methods for handling multilingual support in WordPress.
 */

namespace Axeptio\Plugin\Models;

class I18n {

	/**
	 * Detects if the WordPress installation has multilingual support.
	 *
	 * This method checks if the 'icl_get_languages' function exists,
	 * which is a part of the WPML plugin, to determine if multilingual support is enabled.
	 *
	 * @return bool Returns true if multilingual support is enabled, otherwise false.
	 */
	public static function has_multilangual(): bool {
		return function_exists( 'icl_get_languages' );
	}

	/**
	 * Retrieves the default language of the WordPress installation.
	 *
	 * This method returns the default language set in the WPML plugin if multilingual support is enabled.
	 * If not, it falls back to the locale set in WordPress.
	 *
	 * @return string The default language code.
	 */
	public static function get_default_language(): string {
		return self::has_multilangual() ? icl_get_default_language() : get_locale();
	}

	/**
	 * Retrieves the current language of the visited page.
	 *
	 * This method returns the current language set in the frontend page if multilingual support is enabled.
	 * If not, it returns the current WordPress locale.
	 *
	 * @return string The current language code.
	 */
	public static function get_current_language(): string {
		return self::has_multilangual() ? icl_get_current_language() : get_locale();
	}

	/**
	 * Retrieves a list of available languages for the WordPress installation.
	 *
	 * If multilingual support is enabled, it fetches the list of languages via the WPML plugin.
	 * If not, it returns an array with the current WordPress locale as the only language.
	 * It also reorders the languages array to put the default language first.
	 *
	 * @return array An associative array of languages, each containing 'language_code' and 'option_key_suffix'.
	 */
	public static function get_languages(): array {
		if ( ! self::has_multilangual() ) {
			$lang = get_locale();
			return array(
				array(
					'language_code'     => $lang,
					'option_key_suffix' => '',
				),
			);
		}

		$languages        = self::fetch_languages();
		$default_language = self::get_default_language();

		if ( array_key_exists( $default_language, $languages ) ) {
			$default_lang = $languages[ $default_language ];
			unset( $languages[ $default_language ] );
			$languages = array( $default_language => $default_lang ) + $languages;
		}

		return self::add_option_key_suffix( $languages, $default_language );
	}

	/**
	 * Fetches languages using WPML's 'icl_get_languages' function.
	 *
	 * @return array An array of languages provided by the WPML plugin.
	 */
	private static function fetch_languages(): array {
		return icl_get_languages( 'skip_missing=0&' );
	}

	/**
	 * Adds a suffix to the option keys for each language.
	 *
	 * This method adds a suffix to the option keys for each language,
	 * where the default language does not have a suffix and other languages have their language code as the suffix.
	 *
	 * @param array  $languages Array of language codes.
	 * @param string $default_language The default language code.
	 * @return array An array of languages with updated option key suffixes.
	 */
	private static function add_option_key_suffix( array $languages, string $default_language ): array {
		$output_languages = array();
		foreach ( $languages as $key => $language ) {
			$option_key_suffix        = ( $language['language_code'] === $default_language ) ? '' : '_' . $language['language_code'];
			$output_languages[ $key ] = array_merge( $language, array( 'option_key_suffix' => $option_key_suffix ) );
		}

		return $output_languages;
	}
}
