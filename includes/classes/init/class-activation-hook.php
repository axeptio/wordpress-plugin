<?php
/**
 * Activation Callbacks
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Init;

use Axeptio\Plugin\Models\Settings;
use Axeptio\Plugin\Module;
use Axeptio\Plugin\Utils\Flash_Vars;

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

		if (isset( $_GET['plugin'] ) && isset( $_GET['activate'] )) {
			return;
		}

		$axeptio_plugin_activated  = get_option('axeptio_plugin_activated' );

		if ($axeptio_plugin_activated && ! Settings::get_option('client_id')) {
			delete_option('axeptio_plugin_activated' );
			wp_safe_redirect( admin_url( 'admin.php?page=axeptio-wordpress-plugin&onboard=1' ) );
			exit;
		}
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
	public function set_plugin_activated() {
		update_option('axeptio_plugin_activated', true);
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
		\Axeptio\Plugin\get_template_part( 'admin/onboarding/welcome' );
	}
}
