<?php
/**
 * Class Project_Versions
 *
 * This class provides methods to retrieve project versions.
 */

namespace Axeptio\Models;

class Project_Versions {
	/**
	 * Get all project versions of a client ID.
	 *
	 * @return array[]
	 */
	public static function all() {
		return json_decode( Settings::get_option( 'xpwp_version_options', '', false ) );
	}

	/**
	 * Get the selected project versions.
	 *
	 * This method retrieves the selected project versions by iterating through the localized versions
	 * and fetching their corresponding options from the settings. The selected versions are then returned
	 * as an associative array where the version keys are the array keys and the version values are the
	 * values in the array.
	 *
	 * @return array An associative array of selected project versions.
	 */
	public static function selected_versions(): array {
		$options = array();
		foreach ( self::get_localized_versions() as $option_key ) {
			$options[ $option_key ] = Settings::get_option( $option_key, '' );
		}
		return $options;
	}

	/**
	 * Get the current language version.
	 *
	 * @return string The current language version.
	 */
	public static function get_current_lang_version(): string {

		$localised_version = self::get_localized_versions();
		$current_lang      = i18n::get_current_language();

		$option_key = i18n::has_multilangual() && isset( $localised_version[ $current_lang ] ) ? $localised_version[ $current_lang ] : 'version';

		return apply_filters( 'axeptio/version', Settings::get_option( $option_key, false ), $localised_version, $current_lang );
	}

	/**
	 * Get localized versions for each language.
	 *
	 * @return array Returns an associative array where the keys represent language codes and the values represent localized version keys.
	 * @throws Exception Throws an exception if the language codes are not set correctly.
	 */
	public static function get_localized_versions(): array {
		$languages        = i18n::get_languages();
		$default_language = i18n::get_default_language();

		$client_ids = array(
			$default_language => 'version',
		);

		if ( ! i18n::has_multilangual() ) {
			return $client_ids;
		}

		foreach ( $languages as $lang ) {
			if ( $lang['language_code'] === $default_language ) {
				continue;
			}

			$client_ids[ $lang['language_code'] ] = 'version' . $lang['option_key_suffix'];
		}

		return $client_ids;
	}
}
