<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Admin\Pages;

use Axeptio\Plugin\Admin\Settings\Setting_Api;
use Axeptio\Plugin\Module;

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
				add_action( 'axeptio/before_main_setting_container', array( $this->callbacks, 'add_admin_notice_for_review' ) );
				add_action( 'axeptio/before_plugin_manager_container', array( $this->callbacks, 'add_admin_notice_for_review' ) );
			}
		);

		add_action( 'admin_menu', array( $this, 'override_first_menu_name' ), 90 );
	}

	/**
	 * Override the name of the first menu item under the 'Axeptio' menu.
	 *
	 * @return void
	 */
	public function override_first_menu_name() {
		global $submenu;
		$submenu['axeptio-wordpress-plugin'][0][0] = __( 'Settings', 'axeptio-wordpress-plugin' ); // PHPCS:Ignore WordPress.WP.GlobalVariablesOverride.Prohibited
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

		$main_settings_title = \Axeptio\Plugin\get_template_part(
			'admin/common/fields/title',
			array(
				'title'       => __( 'Widget main settings', 'axeptio-wordpress-plugin' ),
				'description' => __( 'Configure the information related to the integration of your Axeptio project here.', 'axeptio-wordpress-plugin' ),
			),
			false
			);

		$customize_title = \Axeptio\Plugin\get_template_part(
			'admin/common/fields/title',
			array(
				'title'       => __( 'Widget customization', 'axeptio-wordpress-plugin' ),
				'description' => __( 'In this section, you can customize the header texts of the WordPress section in the Axeptio widget.', 'axeptio-wordpress-plugin' ),
			),
			false
			);

		$consent_mode_title = \Axeptio\Plugin\get_template_part(
			'admin/common/fields/title',
			array(
				'title'       => __( 'Google Consent Mode', 'axeptio-wordpress-plugin' ),
				'description' => __( 'In this section, you can set the settings relative to the Google Consent Mode', 'axeptio-wordpress-plugin' ),
			),
			false
		);

		$data_sending_title = \Axeptio\Plugin\get_template_part(
			'admin/common/fields/title',
			array(
				'title'       => __( 'Data sending', 'axeptio-wordpress-plugin' ),
				'description' => __( 'In this section, you can set whether or not you want to let Axeptio collect technical datas', 'axeptio-wordpress-plugin' ),
			),
			false
		);

		$advanced_settings_title = \Axeptio\Plugin\get_template_part(
			'admin/common/fields/title',
			array(
				'title'       => __( 'Advanced Settings', 'axeptio-wordpress-plugin' ),
				'description' => __( 'In this section, you will have the opportunity to manage advanced Axeptio settings.', 'axeptio-wordpress-plugin' ),
			),
			false
		);

		$args = array(
			array(
				'id'    => 'xpwp_admin_index',
				'title' => false,
				'page'  => 'axeptio-wordpress-plugin',
				'args'  => array(
					'before_section' => '<div x-show="currentTab === \'main-settings\'" x-cloak="false">' . $main_settings_title,
				),
			),
			array(
				'id'    => 'xpwp_admin_version',
				'title' => false,
				'page'  => 'axeptio-wordpress-plugin',
				'args'  => array(
					'before_section' => '<div x-show="validAccountID" class="-mt-2">',
					'after_section'  => '</div>',
				),
			),
			array(
				'id'    => 'xpwp_admin_consent_mode',
				'title' => false,
				'page'  => 'axeptio-wordpress-plugin',
				'args'  => array(
					'before_section' => '</div><div x-show="currentTab === \'consent-mode\'" x-cloak>' . $consent_mode_title,
					'after_section'  => '</div>',
				),
			),
			array(
				'id'    => 'xpwp_admin_customize',
				'title' => false,
				'page'  => 'axeptio-wordpress-plugin',
				'args'  => array(
					'before_section' => '<div x-show="currentTab === \'customization\'" x-cloak>' . $customize_title,
					'after_section'  => '</div>',
				),
			),
			array(
				'id'    => 'xpwp_admin_disable_send_datas',
				'title' => false,
				'page'  => 'axeptio-wordpress-plugin',
				'args'  => array(
					'before_section' => '<div x-show="currentTab === \'data-sending\'" x-cloak>' . $data_sending_title,
					'after_section'  => '</div>',
				),
			),
			array(
				'id'    => 'xpwp_admin_advanced_settings',
				'title' => false,
				'page'  => 'axeptio-wordpress-plugin',
				'args'  => array(
					'before_section' => '<div x-show="currentTab === \'advanced-settings\'" x-cloak>' . $advanced_settings_title,
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
			array(
				'id'       => 'xpwp_google_consent_mode',
				'title'    => __( 'Google Consent Mode V2', 'axeptio-wordpress-plugin' ),
				'callback' => array( $this->callbacks, 'google_consent_mode_set' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_consent_mode',
				'args'     => array(
					'label_for' => 'xpwp_google_consent_mode',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_widget_title',
				'title'    => false,
				'callback' => array( $this->callbacks, 'widget_title' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_customize',
				'args'     => array(
					'label_for' => 'xpwp_widget_title',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_widget_subtitle',
				'title'    => false,
				'callback' => array( $this->callbacks, 'widget_subtitle' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_customize',
				'args'     => array(
					'label_for' => 'xpwp_widget_subtitle',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_widget_description',
				'title'    => false,
				'callback' => array( $this->callbacks, 'widget_description' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_customize',
				'args'     => array(
					'label_for' => 'xpwp_widget_description',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_widget_image',
				'title'    => false,
				'callback' => array( $this->callbacks, 'widget_image' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_customize',
				'args'     => array(
					'label_for' => 'xpwp_widget_image',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_widget_background_image',
				'title'    => false,
				'callback' => array( $this->callbacks, 'widget_background_image' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_customize',
				'args'     => array(
					'label_for' => 'xpwp_widget_background_image',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_disable_send_datas',
				'title'    => __( 'Collect of data and errors by Axeptio', 'axeptio-wordpress-plugin' ),
				'callback' => array( $this->callbacks, 'send_datas_set' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_disable_send_datas',
				'args'     => array(
					'label_for' => 'xpwp_disable_send_datas',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_cookie_domain',
				'title'    => false,
				'callback' => array( $this->callbacks, 'cookie_domain' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_advanced_settings',
				'args'     => array(
					'label_for' => 'xpwp_cookie_domain',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_api_url',
				'title'    => false,
				'callback' => array( $this->callbacks, 'api_url' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_advanced_settings',
				'args'     => array(
					'label_for' => 'xpwp_api_url',
					'class'     => 'inline-table-row label-right',
				),
			),
			array(
				'id'       => 'xpwp_proxy_sdk',
				'title'    => false,
				'callback' => array( $this->callbacks, 'proxy_sdk' ),
				'page'     => 'axeptio-wordpress-plugin',
				'section'  => 'xpwp_admin_advanced_settings',
				'args'     => array(
					'label_for' => 'xpwp_proxy_sdk',
					'class'     => 'inline-table-row label-right',
				),
			),
		);

		$this->settings->set_fields( $args );
	}
}
