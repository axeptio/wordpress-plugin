<?php
/**
 * Settings API
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Admin\Settings;

use Axeptio\Plugin\Module;

class Setting_Api {

	/**
	 * Admin pages list.
	 *
	 * @var array
	 */
	public array $admin_pages = array();

	/**
	 * Admin subpages list.
	 *
	 * @var array
	 */
	public array $admin_subpages = array();

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public array $settings = array();

	/**
	 * Admin section.
	 *
	 * @var array
	 */
	public array $sections = array();

	/**
	 * Admin setting fields.
	 *
	 * @var array
	 */
	public array $fields = array();

	/**
	 * Register the admin menu and fields.
	 *
	 * @return void
	 */
	public function register() {
		if ( ! empty( $this->admin_pages ) ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}
		if ( ! empty( $this->settings ) ) {
			add_action( 'admin_init', array( $this, 'register_custom_fields' ) );
		}
	}

	/**
	 * Add admin pages.
	 *
	 * @param array $pages Page list to add.
	 * @return self
	 */
	public function add_pages( array $pages ) {
		$this->admin_pages = $pages;

		return $this;
	}

	/**
	 * Add admin subpages.
	 *
	 * @param string|null $title Title of the subpage.
	 * @return self
	 */
	public function with_subpage( string $title = null ): Setting_Api {
		if ( empty( $this->admin_pages ) ) {
			return $this;
		}
		$admin_page           = $this->admin_pages[0];
		$subpage              = array(
			array(
				'parent_slug' => $admin_page['menu_slug'],
				'page_title'  => $admin_page['page_title'],
				'menu_title'  => ( $title ) ? $title : $admin_page['menu_title'],
				'capability'  => $admin_page['capability'],
				'menu_slug'   => $admin_page['menu_slug'],
				'callback'    => $admin_page['callback'],
			),
		);
		$this->admin_subpages = $subpage;
		return $this;
	}

	/**
	 * Add admin sub pages.
	 *
	 * @param array $pages Subpage list to add.
	 * @return self
	 */
	public function add_sub_pages( array $pages ) {
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );
		return $this;
	}

	/**
	 * Add admin menu
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		foreach ( $this->admin_pages as $page ) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}
		foreach ( $this->admin_subpages as $page ) {
			add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'] );
		}
	}

	/**
	 * Register setttings.
	 *
	 * @param array $settings Settings array.
	 * @return self
	 */
	public function set_settings( array $settings ) {
		$this->settings = $settings;
		return $this;
	}

	/**
	 * Register sections.
	 *
	 * @param array $sections Sections array.
	 * @return self
	 */
	public function set_sections( array $sections ) {
		$this->sections = $sections;
		return $this;
	}

	/**
	 * Register fields.
	 *
	 * @param array $fields Fields array.
	 * @return self
	 */
	public function set_fields( array $fields ) {
		$this->fields = $fields;
		return $this;
	}

	/**
	 * Register custom fields.
	 *
	 * @return void
	 */
	public function register_custom_fields() {
		// register setting.
		foreach ( $this->settings as $setting ) {
			register_setting( $setting['option_group'], $setting['option_name'], $setting['callback'] ?? '' );
		}
		// add settings section.
		foreach ( $this->sections as $section ) {
			add_settings_section( $section['id'], $section['title'], $section['callback'] ?? '', $section['page'], $section['args'] ?? '' );
		}
		// add settings field.
		foreach ( $this->fields as $field ) {
			add_settings_field( $field['id'], $field['title'], $field['callback'] ?? '', $field['page'], $field['section'], $field['args'] ?? '' );
		}
	}
}
