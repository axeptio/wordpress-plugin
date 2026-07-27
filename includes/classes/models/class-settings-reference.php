<?php
/**
 * Settings Reference Model
 *
 * Provides the catalogue of Axeptio SDK options (window.axeptioSettings) exposed
 * by Axeptio at https://static.axept.io/settings-reference.json.
 *
 * It is used to populate the "Advanced Settings" key dropdown and to determine the
 * input type of each value. The remote file is cached in a transient, with a bundled
 * copy shipped inside the plugin as a fallback when the remote fetch fails.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Models;

defined( 'ABSPATH' ) || exit;

class Settings_Reference {

	/**
	 * Remote URL of the settings reference JSON.
	 *
	 * @var string
	 */
	protected static $service_url = 'https://static.axept.io/settings-reference.json';

	/**
	 * Transient key used to cache the remote reference.
	 *
	 * @var string
	 */
	const TRANSIENT_KEY = 'axeptio/settings_reference';

	/**
	 * Path of the bundled fallback JSON, relative to the plugin root.
	 *
	 * @var string
	 */
	const FALLBACK_PATH = 'includes/data/settings-reference.json';

	/**
	 * Properties already managed by the plugin, excluded from the dropdown.
	 *
	 * @var array<string>
	 */
	const EXCLUDED_PROPERTIES = array(
		'clientId',
		'cookiesVersion',
		'userCookiesDomain',
		'postConsentUrl',
		'enableGoogleConsentMode',
		'triggerGTMEvents',
		'sendDatas',
	);

	/**
	 * Setting types that cannot be edited as a static value, excluded from the dropdown.
	 *
	 * @var array<string>
	 */
	const EXCLUDED_TYPES = array(
		'Token',
	);

	/**
	 * In-memory cache of the parsed settings list.
	 *
	 * @var array<int, array>|null
	 */
	private static $settings = null;

	/**
	 * Retrieve the raw list of settings from the reference.
	 *
	 * @return array<int, array> List of setting definitions.
	 */
	public static function all(): array {
		if ( null === self::$settings ) {
			self::$settings = self::fetch_settings();
		}

		return self::$settings;
	}

	/**
	 * Retrieve the settings that can be offered to the merchant.
	 *
	 * Excludes plugin-managed properties and non-editable types, and localizes
	 * the name and description for the current admin locale.
	 *
	 * @return array<int, array{property:string,type:string,name:string,description:string,default:mixed}>
	 */
	public static function get_options(): array {
		$locale  = self::get_locale();
		$options = array();

		foreach ( self::all() as $setting ) {
			if ( ! self::is_selectable( $setting ) ) {
				continue;
			}

			$options[] = array(
				'property'    => $setting['property'],
				'type'        => $setting['type'],
				'name'        => self::localize( $setting['name'] ?? array(), $locale, $setting['property'] ),
				'description' => self::localize( $setting['description'] ?? array(), $locale, '' ),
				'default'     => $setting['default'] ?? null,
			);
		}

		return $options;
	}

	/**
	 * Build a map of every property to its declared type.
	 *
	 * @return array<string, string> Property => type.
	 */
	public static function get_type_map(): array {
		$map = array();

		foreach ( self::all() as $setting ) {
			if ( isset( $setting['property'], $setting['type'] ) ) {
				$map[ $setting['property'] ] = $setting['type'];
			}
		}

		return $map;
	}

	/**
	 * Get the declared type of a given property.
	 *
	 * @param string $property Property name.
	 * @return string|null Type string, or null if unknown.
	 */
	public static function get_type( string $property ): ?string {
		$map = self::get_type_map();

		return $map[ $property ] ?? null;
	}

	/**
	 * Determine whether a setting can be selected by the merchant.
	 *
	 * @param array $setting Setting definition.
	 * @return bool
	 */
	private static function is_selectable( array $setting ): bool {
		if ( empty( $setting['property'] ) || empty( $setting['type'] ) ) {
			return false;
		}

		if ( in_array( $setting['property'], self::EXCLUDED_PROPERTIES, true ) ) {
			return false;
		}

		return ! in_array( $setting['type'], self::EXCLUDED_TYPES, true );
	}

	/**
	 * Resolve a localized value from a `{ en: ..., fr: ... }` map.
	 *
	 * @param array  $values   Locale => string map.
	 * @param string $locale   Preferred locale key (e.g. 'fr').
	 * @param string $fallback Value returned when nothing matches.
	 * @return string
	 */
	private static function localize( array $values, string $locale, string $fallback ): string {
		if ( isset( $values[ $locale ] ) && '' !== $values[ $locale ] ) {
			return (string) $values[ $locale ];
		}

		if ( isset( $values['en'] ) && '' !== $values['en'] ) {
			return (string) $values['en'];
		}

		$first = reset( $values );

		return false === $first ? $fallback : (string) $first;
	}

	/**
	 * Resolve the reference locale key from the current admin locale.
	 *
	 * The reference exposes: en, fr, pt-br, it, es, nl.
	 *
	 * @return string Locale key.
	 */
	private static function get_locale(): string {
		$locale    = strtolower( str_replace( '_', '-', get_user_locale() ) );
		$available = array( 'en', 'fr', 'pt-br', 'it', 'es', 'nl' );

		if ( in_array( $locale, $available, true ) ) {
			return $locale;
		}

		$language = explode( '-', $locale )[0];
		if ( in_array( $language, $available, true ) ) {
			return $language;
		}

		return 'en';
	}

	/**
	 * Fetch and parse the settings list, from cache, remote, or bundled fallback.
	 *
	 * @return array<int, array>
	 */
	private static function fetch_settings(): array {
		$cached = get_transient( self::TRANSIENT_KEY );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$settings = self::fetch_remote();

		if ( null !== $settings ) {
			set_transient( self::TRANSIENT_KEY, $settings, 12 * HOUR_IN_SECONDS );

			return $settings;
		}

		return self::fetch_fallback();
	}

	/**
	 * Fetch the reference from the remote service.
	 *
	 * @return array<int, array>|null Parsed settings, or null on failure.
	 */
	private static function fetch_remote(): ?array {
		$response = wp_remote_get( self::$service_url, array( 'timeout' => 5 ) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		return self::parse( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Read and parse the bundled fallback JSON.
	 *
	 * @return array<int, array>
	 */
	private static function fetch_fallback(): array {
		$path = trailingslashit( XPWP_PATH ) . self::FALLBACK_PATH;

		if ( ! is_readable( $path ) ) {
			return array();
		}

		$parsed = self::parse( (string) file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		return $parsed ?? array();
	}

	/**
	 * Decode a reference JSON payload and extract its settings list.
	 *
	 * @param string $body Raw JSON body.
	 * @return array<int, array>|null Settings list, or null when invalid.
	 */
	private static function parse( string $body ): ?array {
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || ! isset( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
			return null;
		}

		return $data['settings'];
	}
}
