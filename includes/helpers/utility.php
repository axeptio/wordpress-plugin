<?php
/**
 * Utility functions for the plugin.
 *
 * This file is for custom helper functions.
 * These should not be confused with WordPress template
 * tags. Template tags typically use prefixing, as opposed
 * to Namespaces.
 *
 * @link https://developer.wordpress.org/themes/basics/template-tags/
 * @package Axeptio
 */

namespace Axeptio\Plugin\Utility;

defined( 'ABSPATH' ) || exit;

/**
 * Get favicon from URL.
 *
 * @param string $url URL of the website.
 * @return mixed|string
 */
function get_favicon( string $url ) {
	if ( ! $url ) {
		return false;
	}
	$domain = wp_parse_url( $url );

	if ( ! isset(
		$domain['host']
		) ) {
		return false;
	}

	return sprintf( 'https://icon.horse/icon/%s', $domain['host'] );
}
