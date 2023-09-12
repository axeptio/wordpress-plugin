<?php
/**
 * Admin Callbacks
 *
 * @package Axeptio
 */

namespace Axeptio\Admin\Pages;

use Axeptio\Models\Axeptio_Steps;
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
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'active_plugins'   => Plugins::get_active_plugins(),
			'project_versions' => Project_Versions::all(),
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
	public function sdk_active_set() {
		\Axeptio\get_template_part( 'admin/main/fields/sdk-active' );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function send_datas_set() {
		\Axeptio\get_template_part( 'admin/main/fields/send-datas' );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function client_id_set() {
		\Axeptio\get_template_part( 'admin/main/fields/client-id' );
	}

	/**
	 * JSON Options list (hidden field).
	 *
	 * @return void
	 */
	public function version_set_options() {
		\Axeptio\get_template_part( 'admin/main/fields/version-options', array( 'versions' => Settings::get_option( 'xpwp_version_options', '', false ) ) );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function version_set() {
		\Axeptio\get_template_part( 'admin/main/fields/version', array( 'version' => Settings::get_option( 'version', '' ) ) );
	}

	/**
	 * Account panel
	 *
	 * @return void
	 */
	public function display_onboarding_account_panel() {
		\Axeptio\get_template_part( 'admin/onboarding/account' );
	}

	/**
	 * Title of the widget.
	 *
	 * @return void
	 */
	public function widget_title() {
		\Axeptio\get_template_part(
			'admin/common/fields/text',
			array(
				'label' => __( 'Widget title', 'axeptio-wordpress-plugin' ),
				'group' => 'axeptio_settings',
				'name'  => 'widget_title',
				'id'    => 'xpwp_widget_title',
				'value' => Axeptio_Steps::get_title(),
			)
			);
	}

	/**
	 * Sub-title of the widget.
	 *
	 * @return void
	 */
	public function widget_subtitle() {
		\Axeptio\get_template_part(
			'admin/common/fields/text',
			array(
				'label' => __( 'Widget sub-title', 'axeptio-wordpress-plugin' ),
				'group' => 'axeptio_settings',
				'name'  => 'widget_subtitle',
				'id'    => 'xpwp_widget_subtitle',
				'value' => Axeptio_Steps::get_sub_title(),
			)
			);
	}

	/**
	 * Description of the widget.
	 *
	 * @return void
	 */
	public function widget_description() {
		\Axeptio\get_template_part(
			'admin/common/fields/textarea',
			array(
				'label' => __( 'Widget description', 'axeptio-wordpress-plugin' ),
				'group' => 'axeptio_settings',
				'name'  => 'widget_description',
				'id'    => 'xpwp_widget_description',
				'value' => Axeptio_Steps::get_description(),
			)
			);
	}
}
