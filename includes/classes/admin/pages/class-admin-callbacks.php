<?php
/**
 * Admin Callbacks
 *
 * @package Axeptio
 */

namespace Axeptio\Admin\Pages;

use Axeptio\Models\Hook_Modes;
use Axeptio\Models\Plugins;
use Axeptio\Models\Project_Versions;
use Axeptio\Models\Settings;
use Axeptio\Models\Shortcode_Tags_Modes;

class Admin_Callbacks {
	/**
	 * Admin dashboard callback.
	 *
	 * @return resource
	 */
	public function admin_dashboard() {
		return require_once XPWP_PATH . 'templates' . DS . 'admin' . DS . 'settings-main.php';
	}

	/**
	 * Plugin manager callback.
	 *
	 * @return resource
	 */
	public function plugin_manager() {
		$settings = array(
			'nonce'               => wp_create_nonce( 'wp_rest' ),
			'hook_modes'          => Hook_Modes::all(),
			'shortcode_tags_mode' => Shortcode_Tags_Modes::all(),
			'active_plugins'      => Plugins::get_active_plugins(),
			'project_versions'    => Project_Versions::all(),
		);
		return require_once XPWP_PATH . 'templates' . DS . 'admin' . DS . 'plugin-manager.php';
	}

	/**
	 * Options group
	 *
	 * @param mixed $input The input value.
	 * @return mixed
	 */
	public function options_group( $input ) {
		return $input;
	}

	/**
	 * Options page
	 *
	 * @return void
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
		\Axeptio\get_template_part( 'admin/fields/main/version-options', array( 'versions' => Settings::get_option( 'xpwp_version_options', '', false ) ) );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function version_set() {
		\Axeptio\get_template_part( 'admin/fields/main/version', array( 'version' => Settings::get_option( 'version', '' ) ) );
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
