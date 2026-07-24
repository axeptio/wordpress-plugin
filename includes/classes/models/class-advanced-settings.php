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
	 * Each value is cast to its proper PHP type based on the setting's declared type,
	 * so that it serializes to the correct JavaScript type on the frontend.
	 *
	 * @return array<string, mixed> Property => typed value.
	 */
	public static function get_typed(): array {
		$typed = array();

		foreach ( self::get_pairs() as $pair ) {
			if ( empty( $pair['property'] ) ) {
				continue;
			}

			$type                        = $pair['type'] ?? Settings_Reference::get_type( $pair['property'] ) ?? 'string';
			$typed[ $pair['property'] ] = self::cast( $pair['value'] ?? '', (string) $type );
		}

		return $typed;
	}

	/**
	 * Sanitize the pairs before they are persisted.
	 *
	 * Drops rows without a property, removes plugin-managed / non-selectable
	 * properties, de-duplicates on the property (last wins), and keeps only the
	 * `property`, `type` and `value` keys.
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

		foreach ( $pairs as $pair ) {
			if ( ! is_array( $pair ) || empty( $pair['property'] ) ) {
				continue;
			}

			$property = sanitize_text_field( $pair['property'] );

			if ( in_array( $property, Settings_Reference::EXCLUDED_PROPERTIES, true ) ) {
				continue;
			}

			$type = $type_map[ $property ] ?? ( isset( $pair['type'] ) ? sanitize_text_field( $pair['type'] ) : 'string' );

			$sanitized[ $property ] = array(
				'property' => $property,
				'type'     => $type,
				'value'    => self::sanitize_value( $pair['value'] ?? '' ),
			);
		}

		return array_values( $sanitized );
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

	/**
	 * Cast a stored string value to its proper PHP type.
	 *
	 * @param mixed  $value Stored value.
	 * @param string $type  Declared setting type.
	 * @return mixed
	 */
	private static function cast( $value, string $type ) {
		switch ( $type ) {
			case 'boolean':
				return self::to_bool( $value );

			case 'number':
				return self::to_number( $value );

			case 'string[]':
				return self::to_string_list( $value );

			case "boolean | 'update_only'":
				return 'update_only' === $value ? 'update_only' : self::to_bool( $value );

			case "number | 'page' | 'session'":
				return is_numeric( $value ) ? self::to_number( $value ) : (string) $value;

			case 'string':
			default:
				return (string) $value;
		}
	}

	/**
	 * Cast a value to a boolean.
	 *
	 * @param mixed $value Value to cast.
	 * @return bool
	 */
	private static function to_bool( $value ): bool {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Cast a numeric value to int or float.
	 *
	 * @param mixed $value Value to cast.
	 * @return int|float
	 */
	private static function to_number( $value ) {
		if ( ! is_numeric( $value ) ) {
			return 0;
		}

		return ( (float) $value == (int) $value ) ? (int) $value : (float) $value; // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
	}

	/**
	 * Cast a comma-separated string to a list of trimmed, non-empty values.
	 *
	 * @param mixed $value Value to cast.
	 * @return array<int, string>
	 */
	private static function to_string_list( $value ): array {
		if ( is_array( $value ) ) {
			$items = $value;
		} else {
			$items = explode( ',', (string) $value );
		}

		$items = array_map( 'trim', $items );

		return array_values( array_filter( $items, static fn( $item ) => '' !== $item ) );
	}
}
