<?php
/**
 * Axweptio Ajax Process Execution
 */

/**
 * Executing Ajax process.
 */
define( 'DOING_AJAX', true );

if ( ! defined( 'WP_ADMIN' ) ) {
	define( 'WP_ADMIN', true );
}

/** Load WordPress Bootstrap */

$relative_path =  $_REQUEST['relative_path'];

require_once $relative_path . 'wp-load.php';

/** Allow for cross-domain requests (from the front end). */
send_origin_headers();

header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
header( 'X-Robots-Tag: noindex' );

// Require a valid action parameter.
if ( empty( $_REQUEST['action'] ) || ! is_scalar( $_REQUEST['action'] ) ) {
	wp_die( '0', 400 );
}

/** Load WordPress Administration APIs */
require_once ABSPATH . 'wp-admin/includes/admin.php';

/** Load Ajax Handlers for WordPress Core */
require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';

send_nosniff_header();
nocache_headers();

$action = $_REQUEST['action'];

if ( is_user_logged_in() ) {
	// If no action is registered, return a Bad Request response.
	if ( ! has_action( "axeptio/ajax_{$action}" ) ) {
		wp_die( '0', 400 );
	}
	do_action( "axeptio/ajax_{$action}" );
} else {
	// If no action is registered, return a Bad Request response.
	if ( ! has_action( "axeptio/ajax_nopriv_{$action}" ) ) {
		wp_die( '0', 400 );
	}
	do_action( "axeptio/ajax_nopriv_{$action}" );
}

// Default status.
wp_die( '0' );
