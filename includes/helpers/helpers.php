<?php
/**
 * Plugin specific helpers.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin;

use Axeptio\Plugin\Models\Settings;
use Axeptio\Plugin\Utils\Template;

/**
 * Get an initialized class by its full class name, including namespace.
 *
 * @param string $class_name The class name including the namespace.
 *
 * @return false|Module
 */
function get_module( $class_name ) {
	return \Axeptio\Plugin\ModuleInitialization::instance()->get_class( $class_name );
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
 * Get the main admin tabs.
 *
 * This function retrieves an array of main admin tabs with their corresponding labels.
 *
 * @return string The HTML template of the main admin tabs.
 */
function get_main_admin_tabs() {
	$tab_items = array(
		'main-settings' => __( 'Main settings', 'axeptio-wordpress-plugin' ),
		'consent-mode' => __( 'Google Consent Mode', 'axeptio-wordpress-plugin' ),
		'customization' => __( 'Customization', 'axeptio-wordpress-plugin' ),
		'data-sending'  => __( 'Data sending', 'axeptio-wordpress-plugin' ),
		'advanced-settings'  => __( 'Advanced', 'axeptio-wordpress-plugin' ),
	);
	return \Axeptio\Plugin\get_template_part( 'admin/main/tabs', array( 'tab_items' => $tab_items ), false );
}

function get_relative_path($from, $to)
{
	// some compatibility fixes for Windows paths
	$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
	$to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
	$from = str_replace([ABSPATH, '\\'], ['', '/'], $from);
	$to   = str_replace([ABSPATH, '\\'], ['', '/'], $to);

	$from     = explode('/', $from);
	$to       = explode('/', $to);
	$relPath  = $to;

	foreach($from as $depth => $dir) {
		// find first non-matching dir
		if($dir === $to[$depth]) {
			// ignore this directory
			array_shift($relPath);
		} else {
			// get number of remaining dirs to $from
			$remaining = count($from) - $depth;
			if($remaining > 1) {
				// add traversals up to first matching dir
				$padLength = (count($relPath) + $remaining - 1) * -1;
				$relPath = array_pad($relPath, $padLength, '..');
				break;
			} else {
				$relPath[0] = './' . $relPath[0];
			}
		}
	}
	return implode('/', $relPath);
}
