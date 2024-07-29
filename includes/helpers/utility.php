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

/**
 * Get asset info from extracted asset files
 *
 * @param string $slug Asset slug as defined in build/webpack configuration.
 * @param string $attribute Optional attribute to get. Can be version or dependencies.
 * @return string|array
 */
function get_asset_info( $slug, $attribute = null ) {
	if ( file_exists( XPWP_PATH . 'dist/js/' . $slug . '.asset.php' ) ) {
		$asset = require XPWP_PATH . 'dist/js/' . $slug . '.asset.php';
	} elseif ( file_exists( XPWP_PATH . 'dist/css/' . $slug . '.asset.php' ) ) {
		$asset = require XPWP_PATH . 'dist/css/' . $slug . '.asset.php';
	} else {
		return null;
	}

	if ( ! empty( $attribute ) && isset( $asset[ $attribute ] ) ) {
		return $asset[ $attribute ];
	}

	return $asset;
}

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
