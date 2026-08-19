<?php
/**
 * Test bootstrap.
 *
 * Plugin constants come from tests/constants.php, prepended before the
 * Composer autoloader. What is left to set up here is the class autoloader and
 * stubs for the few WordPress functions the tested classes call.
 *
 * @package Axeptio
 */

require_once __DIR__ . '/constants.php';
require_once XPWP_PATH . 'includes/wpcs-autoload.php';

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * Minimal stand-in for the WordPress sanitizer.
	 *
	 * @param mixed $str Raw value.
	 * @return string
	 */
	function sanitize_text_field( $str ) {
		return trim( strip_tags( (string) $str ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
	}
}

if ( ! function_exists( 'wp_parse_url' ) ) {
	/**
	 * Minimal stand-in for the WordPress URL parser.
	 *
	 * @param string $url       URL to parse.
	 * @param int    $component Component to retrieve.
	 * @return mixed
	 */
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component ); // phpcs:ignore WordPress.WP.AlternativeFunctions.parse_url_parse_url
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	/**
	 * Minimal stand-in for the WordPress path helper.
	 *
	 * @param string $value Path.
	 * @return string
	 */
	function trailingslashit( $value ) {
		return rtrim( $value, '/\\' ) . '/';
	}
}

/*
 * Settings_Reference falls back to the bundled JSON when the remote fetch does
 * not answer 200, so these stubs let the tests resolve types against the copy
 * actually shipped with the plugin.
 */
if ( ! function_exists( 'get_transient' ) ) {
	/**
	 * Stand-in returning an always-cold cache.
	 *
	 * @param string $key Transient key.
	 * @return false
	 */
	function get_transient( $key ) {
		return false;
	}
}

if ( ! function_exists( 'wp_remote_get' ) ) {
	/**
	 * Stand-in returning an unusable response.
	 *
	 * @param string $url  Requested URL.
	 * @param array  $args Request arguments.
	 * @return array
	 */
	function wp_remote_get( $url, $args = array() ) {
		return array();
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	/**
	 * Stand-in for the WordPress error check.
	 *
	 * @param mixed $thing Value to check.
	 * @return false
	 */
	function is_wp_error( $thing ) {
		return false;
	}
}

if ( ! function_exists( 'wp_remote_retrieve_response_code' ) ) {
	/**
	 * Stand-in returning a non-200 status, forcing the bundled fallback.
	 *
	 * @param array $response Response.
	 * @return int
	 */
	function wp_remote_retrieve_response_code( $response ) {
		return 0;
	}
}
