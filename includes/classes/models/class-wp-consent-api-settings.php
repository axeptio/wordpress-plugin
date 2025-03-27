<?php
/**
 * WP Consent API Model
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Models;

/**
 * Class WP_Consent_API_Settings
 *
 * Handles the WP Consent API settings and compatibility checks.
 *
 * @package Axeptio\Plugin\Models
 */
class WP_Consent_API_Settings {
	/**
	 * Check if a plugin is compatible with WP Consent API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_key Plugin key.
	 * @return array WP Consent API compatibility information.
	 */
	public static function find( string $plugin_key ): array {
		$plugin_basename = plugin_basename( $plugin_key );

		return [
			'is_compliant'  => (bool) apply_filters( "wp_consent_api_registered_{$plugin_basename}", false ),
			'consent_type'  => apply_filters( 'wp_get_consent_type', false ),
			'categories'    => self::get_consent_categories(),
		];
	}

	/**
	 * Get available consent categories.
	 *
	 * @since 1.0.0
	 *
	 * @return array Consent categories.
	 */
	private static function get_consent_categories(): array {
		return [
			'functional',
			'preferences',
			'statistics',
			'statistics-anonymous',
			'marketing',
		];
	}
}
