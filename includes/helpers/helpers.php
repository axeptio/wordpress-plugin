<?php
/**
 * Plugin specific helpers.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin;

defined( 'ABSPATH' ) || exit;

use Axeptio\Plugin\Models\Settings;
use Axeptio\Plugin\Utils\Template;

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
		'main-settings'     => __( 'Main settings', 'axeptio-sdk-integration' ),
		'consent-mode'      => __( 'Google Consent Mode', 'axeptio-sdk-integration' ),
		'customization'     => __( 'Customization', 'axeptio-sdk-integration' ),
		'data-sending'      => __( 'Data sending', 'axeptio-sdk-integration' ),
		'advanced-settings' => __( 'Advanced', 'axeptio-sdk-integration' ),
	);
	return \Axeptio\Plugin\get_template_part( 'admin/main/tabs', array( 'tab_items' => $tab_items ), false );
}

/**
 * Get the SDK URL.
 *
 * @return string The SDK URL.
 */
function get_sdk_url() {
	if ( Settings::get_option( 'proxy_sdk', false ) ) {
		$proxy_key = \get_option( 'axeptio/sdk_proxy_key' );
		return home_url() . '/' . $proxy_key . '.js';
	}
	return 'https://static.axept.io/sdk.js';
}

/**
 * Get the plugin REST API root, site relative, for callers to append a route to.
 *
 * @param string $namespace REST namespace.
 * @return string The REST root, with a trailing slash.
 */
function get_rest_root( string $namespace = 'axeptio/v1' ): string {
	return esc_url_raw( wp_make_link_relative( trailingslashit( rest_url( $namespace ) ) ) );
}

/**
 * Determines if the current request is a WordPress REST API request.
 *
 * Handles various scenarios to ensure compatibility:
 * - REST_REQUEST constant is defined (direct REST API request).
 * - The `rest_route` parameter exists (support for plain permalinks).
 * - WP_Rewrite might not be initialized yet.
 * - The URL path starts with the REST API prefix (e.g., `wp-json/`).
 *
 * @return bool True if the request is a REST API request, false otherwise.
 */
function is_rest(): bool {
	// Check if the REST_REQUEST constant is defined and true.
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return true;
	}

	// Check if the `rest_route` parameter exists and starts with a forward slash.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- This is a read-only check for REST API detection.
	if ( isset( $_GET['rest_route'] ) && is_string( $_GET['rest_route'] ) && 0 === strpos( sanitize_text_field( wp_unslash( $_GET['rest_route'] ) ), '/' ) ) {
		return true;
	}

	// Initialize WP_Rewrite if not already initialized.
	global $wp_rewrite;
	if ( null === $wp_rewrite ) {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required for REST detection before WP_Rewrite is initialized.
		$wp_rewrite = new WP_Rewrite();
	}

	// Compare the current URL path with the REST API base path.
	$rest_url    = wp_parse_url( trailingslashit( rest_url() ) );
	$current_url = wp_parse_url( add_query_arg( array() ) );

	// Ensure both paths are available before comparing.
	if ( isset( $rest_url['path'], $current_url['path'] ) ) {
		return 0 === strpos( $current_url['path'], $rest_url['path'] );
	}

	return false;
}
