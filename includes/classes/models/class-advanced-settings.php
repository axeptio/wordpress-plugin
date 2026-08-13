<?php
/**
 * Advanced Settings Model
 *
 * Handles the merchant-configured key/value pairs injected into window.axeptioSettings.
 * Pairs are stored under the `axeptio_settings` option group, in the `advanced_settings`
 * key, as a list of `{ property, type, value }` entries.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Models;

defined( 'ABSPATH' ) || exit;

class Advanced_Settings {

	/**
	 * Option key holding the pairs, within the `axeptio_settings` group.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'advanced_settings';

	/**
	 * Properties dropped by the last sanitize() run.
	 *
	 * Reporting them is left to the caller: sanitize() runs on every writer of the
	 * option, and only the admin form save has a merchant to warn.
	 *
	 * @var array<int, string>
	 */
	private static array $rejected = array();

	/**
	 * Retrieve the raw stored pairs.
	 *
	 * @return array<int, array{property:string,type:string,value:mixed}>
	 */
	public static function get_pairs(): array {
		$pairs = Settings::get_option( self::OPTION_KEY, array() );

		return is_array( $pairs ) ? array_values( $pairs ) : array();
	}

	/**
	 * Build the map of typed settings ready to be merged into the SDK settings.
	 *
	 * Pairs holding an empty or mistyped value are skipped rather than cast: they
	 * would reach window.axeptioSettings as `0`, `false` or an empty string and
	 * override a key the banner may need to render. This also covers pairs stored
	 * before the value became mandatory.
	 *
	 * @return array<string, mixed> Property => typed value.
	 */
	public static function get_typed(): array {
		$typed = array();

		foreach ( self::get_pairs() as $pair ) {
			if ( empty( $pair['property'] ) ) {
				continue;
			}

			$type  = (string) ( $pair['type'] ?? Setting_Type::STRING );
			$value = $pair['value'] ?? '';

			if ( ! self::is_valid_value( (string) $pair['property'], $type, $value ) ) {
				continue;
			}

			$typed[ $pair['property'] ] = Setting_Type::cast( $type, $value );
		}

		return $typed;
	}

	/**
	 * Sanitize the pairs before they are persisted.
	 *
	 * De-duplicates on the property, last one wins. The admin UI blocks invalid
	 * rows before submission; this is the guard for anything reaching the option
	 * another way.
	 *
	 * @param mixed $pairs Raw pairs coming from the settings form.
	 * @return array<int, array{property:string,type:string,value:string}>
	 */
	public static function sanitize( $pairs ): array {
		if ( ! is_array( $pairs ) ) {
			return array();
		}

		$type_map  = Settings_Reference::get_type_map();
		$sanitized = array();
		$rejected  = array();

		foreach ( $pairs as $pair ) {
			if ( ! self::is_storable( $pair ) ) {
				continue;
			}

			$property = sanitize_text_field( $pair['property'] );
			$type     = self::resolve_type( $property, $pair, $type_map );
			$value    = self::sanitize_value( $pair['value'] ?? '' );

			if ( ! self::is_valid_value( $property, $type, $value ) ) {
				$rejected[] = $property;
				continue;
			}

			$sanitized[ $property ] = array(
				'property' => $property,
				'type'     => $type,
				'value'    => Setting_Type::normalize( $type, $value ),
			);
		}

		self::$rejected = array_values( array_diff( array_unique( $rejected ), array_keys( $sanitized ) ) );

		return array_values( $sanitized );
	}

	/**
	 * Properties dropped by the last sanitize() run.
	 * @return array<int, string>
	 */
	public static function get_rejected(): array {
		return self::$rejected;
	}

	/**
	 * The reference declares a type but no format, so the `…Url` suffix of a
	 * property name is the only signal that its value has to be a URL.
	 *
	 * @param string $property Property name.
	 * @param string $type     Declared setting type.
	 * @param mixed  $value    Value to check.
	 * @return bool
	 */
	private static function is_valid_value( string $property, string $type, $value ): bool {
		if ( ! Setting_Type::validate( $type, $value ) ) {
			return false;
		}

		if ( 'url' === strtolower( substr( $property, -3 ) ) ) {
			return self::is_url( (string) $value );
		}

		return true;
	}

	/**
	 * The SDK calls these endpoints directly, so only an absolute http(s) URL on
	 * a domain name is accepted. filter_var alone lets `javascript:` and a
	 * dotless host such as `https://test` through.
	 *
	 * @param string $value Value to check.
	 * @return bool
	 */
	private static function is_url( string $value ): bool {
		if ( 1 !== preg_match( '#^https?://#i', $value ) ) {
			return false;
		}

		if ( false === filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		$host = wp_parse_url( $value, PHP_URL_HOST );

		return is_string( $host ) && false !== strpos( $host, '.' );
	}

	/**
	 * Determine whether a submitted row belongs in the option at all.
	 *
	 * @param mixed $pair Raw row.
	 * @return bool
	 */
	private static function is_storable( $pair ): bool {
		if ( ! is_array( $pair ) || empty( $pair['property'] ) ) {
			return false;
		}

		$property = sanitize_text_field( $pair['property'] );

		return ! in_array( $property, Settings_Reference::EXCLUDED_PROPERTIES, true );
	}

	/**
	 * Resolve the type of a row, trusting the reference over what was submitted.
	 *
	 * @param string               $property Property name.
	 * @param array<string, mixed> $pair     Raw row.
	 * @param array<string, string> $type_map Property => type, from the reference.
	 * @return string
	 */
	private static function resolve_type( string $property, array $pair, array $type_map ): string {
		if ( isset( $type_map[ $property ] ) ) {
			return $type_map[ $property ];
		}

		if ( isset( $pair['type'] ) ) {
			return sanitize_text_field( $pair['type'] );
		}

		return Setting_Type::STRING;
	}

	/**
	 * Sanitize a raw value, preserving simple scalar content.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	private static function sanitize_value( $value ): string {
		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}

		return sanitize_text_field( (string) $value );
	}
}
