<?php
/**
 * Axweptio Ajax Process Execution
 */

/**
 * Executing Ajax process.
 */
define( 'AXEPTIO_DOING_AJAX', true );

if ( ! defined( 'AXEPTIO_WP_ADMIN' ) ) {
	define( 'AXEPTIO_WP_ADMIN', true );
}

/** Load WordPress Bootstrap */

// Define a prefixed constant.
define( 'AXEPTIO_DOING_AJAX', true );
define( 'AXEPTIO_WP_ADMIN', false );

// Use wp_unslash and sanitize_text_field.
$axeptio_relative_path = isset( $_REQUEST['relative_path'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['relative_path'] ) ) : '';

// Vérification du nonce.
if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'axeptio_ajax_nonce' ) ) {
	wp_die( 'Security check failed', 403 );
}

require_once $axeptio_relative_path . 'wp-load.php';

/** Allow for cross-domain requests (from the front end). */
send_origin_headers();

header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
header( 'X-Robots-Tag: noindex' );

$axeptio_action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';

if ( empty( $axeptio_action ) || ! is_scalar( $axeptio_action ) ) {
	wp_die( '0', 400 );
}

/** Load WordPress Administration APIs */
require_once ABSPATH . 'wp-admin/includes/admin.php';

/** Load Ajax Handlers for WordPress Core */
require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';

send_nosniff_header();
nocache_headers();

if ( is_user_logged_in() ) {
	// If no action is registered, return a Bad Request response.
	if ( ! has_action( "axeptio/ajax_{$axeptio_action}" ) ) {
		wp_die( '0', 400 );
	}
	do_action( "axeptio/ajax_{$axeptio_action}" );
} else {
	// If no action is registered, return a Bad Request response.
	if ( ! has_action( "axeptio/ajax_nopriv_{$axeptio_action}" ) ) {
		wp_die( '0', 400 );
	}
	do_action( "axeptio/ajax_nopriv_{$axeptio_action}" );
}

// Default status.
wp_die( '0' );
