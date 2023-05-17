<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Admin\Pages;

use Axeptio\Admin\Settings\Setting_Api;
use Axeptio\Module;

class Admin_Main extends Module {

	/**
	 * Initializing the setting api.
	 *
	 * @var Setting_Api
	 */
	private Setting_Api $settings;

	/**
	 * Admin callback manager.
	 *
	 * @var Admin_Callbacks
	 */
	private Admin_Callbacks $callbacks;

	/**
	 * Admin page list.
	 *
	 * @var array $pages
	 */
	private array $pages = array();

	/**
	 * Admin subpage list.
	 *
	 * @var array $subpages
	 */
	private array $subpages = array();

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
		add_action(
			'init',
			function () {
				$this->settings  = new Setting_Api();
				$this->callbacks = new Admin_Callbacks();
				$this->set_pages();
				$this->set_settings();
				$this->set_sections();
				$this->set_fields();
				$this->settings
					->add_pages( $this->pages )
					->add_sub_pages( $this->subpages )
					->register();

				add_action( 'axeptio/after_main_settings', array( $this->callbacks, 'display_onboarding_account_panel' ) );
			}
		);
	}

	/**
	 * Set admin pages.
	 *
	 * @return void
	 */
	public function set_pages() {
		$this->pages = array(
			array(
				'page_title' => __( 'Axeptio Widget Integration', 'axeptio-wordpress-plugin' ),
				'menu_title' => __( 'Axeptio', 'axeptio-wordpress-plugin' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'axeptio-wordpress-plugin',
				'callback'   => array( $this->callbacks, 'admin_dashboard' ),
				'icon_url'   => XPWP_URL . 'dist/img/icon.png',
				'position'   => 110,
			),
		);

		$this->subpages = array(
			array(
				'page_title'  => __( 'Plugin manager', 'axeptio-wordpress-plugin' ),
				'menu_title'  => __( 'Plugin manager', 'axeptio-wordpress-plugin' ),
				'capability'  => 'manage_options',
				'parent_slug' => 'axeptio-wordpress-plugin',
				'menu_slug'   => 'axeptio-plugin-manager',
				'callback'    => array( $this->callbacks, 'plugin_manager' ),
			),
		);
	}

	/**
	 * Set admin settings.
	 *
	 * @return void
	 */
	public function set_settings() {
		$args = array(
			array(
				'option_group' => 'xpwp_settings_group',
				'option_name'  => 'axeptio_settings',
				'callback'     => array( $this->callbacks, 'options_group' ),
			),
			array(
				'option_group' => 'xpwp_settings_group',
				'option_name'  => 'xpwp_version_options',
				'callback'     => array( $this->callbacks, 'options_group' ),
			),
		);
		$this->settings->set_settings( $args );
	}

	/**
	 * Set admin sections.
	 *
	 * @return void
	 */
	public function set_sections() {
		$args = array(
			array(
				'id'    => 'xpwp_admin_index',
				'title' => __( 'Widget settings', 'axeptio-wordpress-plugin' ),
				'page'  => 'axeptio-wordpress-plugin',
			),
			array(
				'id'    => 'xpwp_admin_version',
				'title' => false,
				'page'  => 'axeptio-wordpress-plugin',
				'args'  => array(
					'before_section' => '<div x-show="validAccountID">',
					'after_section'  => '</div>',
				),
			),
		);
		$this->settings->set_sections( $args );
	}

	/**
	 * Set admin fields.
	 *
	 * @return void
	 */
	public function set_fields() {
		$args = array(
			array(
				'id'       => 'xpwp_sdk_active',
				'title'    => __( 'Do you want to enable the widget?', 'axeptio-wordpress-plugin' ),
				'callback' => array( $this->callbacks, 'sdk_active_set' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_index',
				'args'     => array(
					'label_for' => 'xpwp_sdk_active',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_client_id',
				'title'    => __( 'Project ID', 'axeptio-wordpress-plugin' ),
				'callback' => array( $this->callbacks, 'client_id_set' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_index',
				'args'     => array(
					'label_for' => 'xpwp_client_id',
					'class'     => 'inline-table-row',
				),
			),
			array(
				'id'       => 'xpwp_version_options',
				'title'    => __( 'Options', 'axeptio-wordpress-plugin' ),
				'callback' => array( $this->callbacks, 'version_set_options' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_index',
				'args'     => array(
					'label_for' => 'xpwp_sdk_active',
					'class'     => 'hidden',
				),
			),
			array(
				'id'       => 'xpwp_version',
				'title'    => __( 'Project Version', 'axeptio-wordpress-plugin' ),
				'callback' => array( $this->callbacks, 'version_set' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_version',
				'args'     => array(
					'label_for' => 'xpwp_version',
					'class'     => 'inline-table-row',
				),
			),
		);

		$this->settings->set_fields( $args );
	}
}
