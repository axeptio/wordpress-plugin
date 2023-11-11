<?php
/**
 * Plugins Model
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Client_Id {
	/**
	 * Get all client ids.
	 *
	 * @return array[]
	 */
	public static function all(): array {
		$languages        = i18n::get_languages();
		$default_language = i18n::get_default_language();
		$client_ids       = array();
		$client_ids[]     = Settings::get_option( 'client_id', '' );
		if ( i18n::has_multilangual() ) {
			return $client_ids;
		}

		foreach ( $languages as $lang ) {
			if ( $lang['language_code'] !== $default_language ) {
				continue;
			}
			$client_ids[] = Settings::get_option( 'client_id' . $lang['option_key_suffix'], '' );
		}

		return $client_ids;
	}
}
