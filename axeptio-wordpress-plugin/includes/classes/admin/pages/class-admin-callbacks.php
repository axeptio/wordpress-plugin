<?php
/**
 * Admin Callbacks
 *
 * @package Axeptio
 */

namespace Axeptio\Admin\Pages;

class Admin_Callbacks {
	/**
	 * Init
	 *
	 * @return resource
	 */
	public function admin_dashboard() {
		return require_once XPWP_PATH . DS . 'templates' . DS . 'admin' . DS . 'settings-main.php';
	}

	/**
	 * Options group
	 *
	 * @param string $input The input value.
	 * @return string
	 */
	public function options_group( $input ) {
		return $input;
	}

	/**
	 * Options page
	 *
	 * @echo string
	 */
	public function admin_section() {
		\Axeptio\get_template_part( 'admin/fields/main/admin-section' );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function sdk_active_set() {
		\Axeptio\get_template_part( 'admin/fields/main/sdk-active' );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function client_id_set() {
		\Axeptio\get_template_part( 'admin/fields/main/client-id' );
	}

	/**
	 * JSON Options list (hidden field).
	 *
	 * @return void
	 */
	public function version_set_options() {
		\Axeptio\get_template_part( 'admin/fields/main/version-options', array( 'versions' => get_option( 'xpwp_version_options' ) ) );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function version_set() {
		\Axeptio\get_template_part( 'admin/fields/main/version', array( 'version' => get_option( 'xpwp_version' ) ) );
	}

	/**
	 * Account panel
	 *
	 * @return void
	 */
	public function display_onboarding_account_panel() {
		\Axeptio\get_template_part( 'admin/onboarding/account' );
	}
}
