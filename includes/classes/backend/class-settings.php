<?php
/**
 * Class Settings
 *
 * This class extends the Module class and provides functionality related to cookies.
 *
 * @package Axeptio\Plugin\Backend
 */

namespace Axeptio\Plugin\Backend;

use Axeptio\Plugin\Models\Project_Versions;
use Axeptio\Plugin\Module;

class Settings extends Module {
	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Method register
	 *
	 * Registers the updated option action
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'update_option_axeptio_settings', array( $this, 'historize_version' ), 20, 2 );
		add_action( 'pre_update_option_axeptio_settings', array( $this, 'sanitize_domain' ), 20, 2 );
	}

	/**
	 * Sanitizes the cookie domain in the settings.
	 *
	 * This function removes common URL prefixes ('https://', 'http://', '//') from the cookie domain
	 * to ensure it's in the correct format.
	 *
	 * @param array $new_value The new settings value being saved.
	 * @param array $old_value The old settings value before update.
	 * @return array The sanitized settings value.
	 */
	public function sanitize_domain( $new_value, $old_value ) {
		if ( ! isset( $new_value['cookie_domain'] ) ) {
			return $new_value;
		}

		$new_value['cookie_domain'] = str_replace( array( 'https://', 'http://', '//' ), '', $new_value['cookie_domain'] );

		return $new_value;
	}

	/**
	 * Method historize_version
	 *
	 * Saves the previous version of the settings when the settings are updated.
	 *
	 * @param mixed $old_value The old version of the settings.
	 * @param mixed $new_value The new version of the settings.
	 * @return void
	 */
	public function historize_version( $old_value, $new_value ) {
		$axeptio_localized_version = Project_Versions::get_localized_versions();

		$datas = array();
		foreach ( $axeptio_localized_version as $option_key ) {
			$datas[ $option_key ] = $old_value[ $option_key ];
		}

		$axeptio_versions                            = get_option( 'axeptio_versions' );
		$axeptio_versions[ $old_value['client_id'] ] = $datas;
		update_option( 'axeptio_versions', $axeptio_versions );
	}
}
