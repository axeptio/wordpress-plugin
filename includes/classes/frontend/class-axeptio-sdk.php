<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Frontend;

use Axeptio\Models\Settings;
use Axeptio\Module;
use function Axeptio\get_sdk_settings;

class Axeptio_Sdk extends Module {
	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Registering the admin page.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue the SDK scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$settings = $this->get_sdk_settings();

		if ( ! $settings ) {
			return;
		}

		wp_register_script( 'axeptio/sdk-script', '', array(), XPWP_VERSION, true );
		wp_enqueue_script( 'axeptio/sdk-script' );
		wp_localize_script( 'axeptio/sdk-script', 'Axeptio_SDK', $settings );

		$inline_script = \Axeptio\get_template_part( 'frontend/sdk', array(), false );

		wp_add_inline_script( 'axeptio/sdk-script', $inline_script, 'after' );
	}

	/**
	 * Retrieve the SDK settings.
	 *
	 * @return array|false Settings of the SDK.
	 */
	private function get_sdk_settings() {
		$sdk_active = (bool) Settings::get_option( 'xpwp_sdk_active', false );

		$client_id       = Settings::get_option( 'xpwp_client_id', false );
		$cookies_version = Settings::get_option( 'xpwp_version', false );

		if ( ! $sdk_active || ( ! $client_id && ! $cookies_version ) ) {
			return false;
		}

		return apply_filters(
			'axeptio/sdk_settings',
			array(
				'clientId'       => $client_id,
				'cookiesVersion' => $cookies_version,
			)
		);
	}
}
