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

namespace Axeptio\Utility;

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
 * @param int    $size   Size of the icon.
 * @param int    $expire Cache expiration in seconds.
 * @return mixed|string
 */
function get_favicon( string $url, int $size = 32, int $expire = 86400 ) {
	if ( ! $url ) {
		return false;
	}
	$domain = wp_parse_url( $url );

	$icon_url = "https://www.google.com/s2/favicons?domain={$domain['host']}&sz={$size}";

	$cache_key = 'xpwpd_favicon_' . md5( $url );

	$cached_favicon_url = get_transient( $cache_key );

	if ( $cached_favicon_url ) {
		return $cached_favicon_url;
	}

	set_transient( $cache_key, $icon_url, $expire );

	return $icon_url;
}

