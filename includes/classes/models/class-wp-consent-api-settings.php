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
	 * Check if the WP Consent API plugin is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the WP Consent API plugin is active, false otherwise.
	 */
	public static function is_active(): bool {

		return function_exists( 'is_plugin_active' ) && is_plugin_active( 'wp-consent-api/wp-consent-api.php' );
	}

	/**
	 * Get categories data with titles and descriptions.
	 * @since 2.6.2
	 * @return array Categories data.
	 */
	private static function get_categories_data(): array {
		return [
			'functional'          => [
				'title'       => __( 'Functional', 'axeptio-wordpress-plugin' ),
				'description' => __( 'The cookie or any other form of local storage is used for the sole purpose of carrying out the transmission of a communication over an electronic communications network.', 'axeptio-wordpress-plugin' ),
			],
			'preferences'         => [
				'title'       => __( 'Preferences', 'axeptio-wordpress-plugin' ),
				'description' => __( 'Cookies or any other form of local storage that can not be seen as statistics, statistics-anonymous, marketing or functional, and where the technical storage or access is necessary for the legitimate purpose of storing preferences.', 'axeptio-wordpress-plugin' ),
			],
			'statistics'          => [
				'title'       => __( 'Statistics', 'axeptio-wordpress-plugin' ),
				'description' => __( 'Cookies or any other form of local storage that are used exclusively for statistical purposes (Analytics Cookies).', 'axeptio-wordpress-plugin' ),
			],
			'statistics-anonymous' => [
				'title'       => __( 'Anonymous Statistics', 'axeptio-wordpress-plugin' ),
				'description' => __( 'Cookies or any other form of local storage that are used exclusively for anonymous statistical purposes.', 'axeptio-wordpress-plugin' ),
			],
			'marketing'           => [
				'title'       => __( 'Marketing', 'axeptio-wordpress-plugin' ),
				'description' => __( 'Cookies or any other form of local storage required to create user profiles to send advertising or to track the user on a website or across websites for similar marketing purposes.', 'axeptio-wordpress-plugin' ),
			],
		];
	}

	/**
	 * Get available consent categories.
	 * @since 1.0.0
	 * @return array Consent categories.
	 */
	public static function get_consent_categories(): array {
		return array_keys( self::get_categories_data() );
	}

	/**
	 * Get WP Consent API categories as virtual vendors for the Axeptio widget.
	 * @since 2.6.2
	 * @return array Array of category vendors.
	 */
	public static function get_category_vendors(): array {
		$categories_data = self::get_categories_data();

		$vendors = [];
		foreach ( $categories_data as $category => $data ) {
			$vendors[] = [
				'name'                => 'wp_consent_' . str_replace( '-', '_', $category ),
				'title'               => $data['title'],
				'shortDescription'    => $data['description'],
				'longDescription'     => '',
				'policyUrl'           => '',
				'domain'              => '',
				'image'               => \Axeptio\Plugin\Utility\get_favicon( 'https://wordpress.org' ),
				'type'                => 'wp_consent_category',
				'step'                => 'wordpress',
				'isWpConsentCategory' => true,
				'wpConsentCategory'   => $category,
			];
		}

		return $vendors;
	}
}
