<?php
/**
 * Activation Callbacks
 *
 * @package Axeptio
 */

namespace Axeptio\Init;

use Axeptio\Module;
use function Axeptio\get_current_admin_url;

class Activation_Hook extends Module {

	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Registering the activated hook.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'after_plugin_activation' ) );
		add_action( 'axeptio/before_main_setting_container', array( $this, 'display_onboarding_panel' ) );
		$this->run_upgrade_scripts();
	}

	/**
	 * Methods to be run after the plugin is activated.
	 *
	 * @return void
	 */
	public function after_plugin_activation() {
		$this->maybe_redirect_to_settings_page();
	}

	/**
	 * Methods to be run after the plugin is activated.
	 *
	 * @return void
	 */
	public function run_upgrade_scripts() {
	}

	/**
	 * Maybe redirect to settings page.
	 *
	 * @return void
	 */
	public function maybe_redirect_to_settings_page() {
		$current_url = get_current_admin_url();

		$activated_by_wpdmin = ! empty( strpos( $current_url, 'plugins.php' ) ) && isset( $_GET['activate'] ) && (bool) sanitize_text_field( wp_unslash( $_GET['activate'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! $activated_by_wpdmin || isset( $_GET['activate-multi'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		if ( get_option( 'xpwp_client_id' ) === false ) {
			wp_safe_redirect( admin_url( 'admin.php?page=axeptio-wordpress-plugin&onboard=1' ) );
			exit;
		}
	}

	/**
	 * Display the onboarding panel.
	 *
	 * @return void
	 */
	public function display_onboarding_panel() {
		if ( ! isset( $_GET['onboard'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}
		\Axeptio\get_template_part( 'admin/onboarding/welcome' );
	}
}
