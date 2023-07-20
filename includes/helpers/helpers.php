<?php
/**
 * Plugin specific helpers.
 *
 * @package Axeptio
 */

namespace Axeptio;

use Axeptio\Models\Settings;
use Axeptio\Utils\Template;

/**
 * Get an initialized class by its full class name, including namespace.
 *
 * @param string $class_name The class name including the namespace.
 *
 * @return false|Module
 */
function get_module( $class_name ) {
	return \Axeptio\ModuleInitialization::instance()->get_class( $class_name );
}

/**
 * Get the base URL of the current admin page, with query params.
 *
 * @return string
 */
function get_current_admin_url(): string {
	$home_url   = wp_parse_url( home_url() );
	$query_args = add_query_arg( null, null );

	if (
		is_array( $home_url )
		&& isset( $home_url['path'] )
	) {
		$query_args = str_replace( $home_url['path'], '', $query_args );
	}

	return home_url( $query_args );
}

/**
 * Get the logo.
 *
 * @return string
 */
function get_logo(): string {
	return XPWP_URL . 'dist/img/logo.svg';
}

/**
 * Get option shortcut
 *
 * @param string       $slug          Option slug to retrieve.
 * @param mixed        $default_value Default value to return if the option is not set.
 * @param string|false $group   Option group (if false, single value option).
 * @return false|mixed|null
 */
function get_option( string $slug, $default_value = null, $group = 'axeptio_settings' ) {
	return Settings::get_option( $slug, $default_value, $group );
}

/**
 * Get an image from the assets folder.
 *
 * @param string $path Path the image file.
 *
 * @return string
 */
function get_img( $path ): string {
	return XPWP_URL . 'dist/img/' . $path;
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * WC_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
 *
 * @param mixed       $slug Template slug.
 * @param string      $datas Template datas to pass.
 * @param string|void $display Return or echo the template, default to echo.
 */
function get_template_part( $slug, $datas = array(), $display = true ) {
	// Create a new Template instance and set the template data.
	$template = ( new Template() )->set_template_data( $datas );

	// If $echo is false, start output buffering.
	if ( ! $display ) {
		ob_start();
	}

	// Get the template part.
	$template->get_template_part( $slug );

	// If $echo is false, end output buffering and return the contents.
	if ( ! $display ) {
		$content = ob_get_clean();
		return $content;
	}
}


	/**
	 * Retrieves the cookie domain.
	 *
	 * By default, the cookie domain is fetched from the 'cookie_domain' option in the WordPress database.
	 * If the option is empty, the home URL is used.
	 * The function replaces any 'https://' in the cookie domain with 'http://'.
	 *
	 * @return string The cookie domain.
	 */
function get_cookie_domain(): string {
	$cookie_domain = get_option( 'cookie_domain', home_url() );
	return str_replace(
		array( 'https://', 'http://' ),
		'',
		$cookie_domain
	);
}
