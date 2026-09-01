<?php
/**
 * Setting Type Model
 *
 * Resolves the types declared by the Axeptio settings reference into a primitive
 * and an optional list of literal members, so that a union such as
 * `boolean | 'forced'` is handled like any other type instead of failing an exact
 * string comparison.
 *
 * Mirrored client side in `assets/js/backend/utils/settingType.js`; both are held
 * to the cases in `tests/fixtures/setting-types.json`.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Models;

defined( 'ABSPATH' ) || exit;

class Setting_Type {

	const BOOLEAN     = 'boolean';
	const NUMBER      = 'number';
	const STRING      = 'string';
	const STRING_LIST = 'string[]';

	const PRIMITIVES = array( self::BOOLEAN, self::NUMBER, self::STRING, self::STRING_LIST );

	/**
	 * Resolved types, keyed by declared type: get_typed() runs on every frontend
	 * page load, over a reference holding a handful of distinct types.
	 *
	 * @var array<string, array{primitive:string|null,literals:array<int,string>}>
	 */
	private static $parsed = array();

	/**
	 * A type holding no known primitive resolves to a null one, which callers
	 * treat as free text.
	 *
	 * @param string $type Declared setting type.
	 * @return array{primitive:string|null,literals:array<int,string>}
	 */
	public static function parse( string $type ): array {
		if ( ! isset( self::$parsed[ $type ] ) ) {
			self::$parsed[ $type ] = self::split( $type );
		}

		return self::$parsed[ $type ];
	}

	/**
	 * @param string $type  Declared setting type.
	 * @param mixed  $value Value to check.
	 * @return bool
	 */
	public static function is_literal( string $type, $value ): bool {
		if ( ! is_string( $value ) ) {
			return false;
		}

		return in_array( $value, self::parse( $type )['literals'], true );
	}

	/**
	 * An empty value can never be stored: it would be cast to `0`, `false` or an
	 * empty string and override the key in window.axeptioSettings.
	 *
	 * @param string $type  Declared setting type.
	 * @param mixed  $value Value to check.
	 * @return bool
	 */
	public static function validate( string $type, $value ): bool {
		if ( is_bool( $value ) ) {
			return true;
		}

		$value = (string) $value;

		if ( '' === $value ) {
			return false;
		}

		if ( self::is_literal( $type, $value ) ) {
			return true;
		}

		return self::matches_primitive( $type, $value );
	}

	/**
	 * Booleans are unified on `1` / `0` whatever notation they came in with. An
	 * empty value is returned as-is: telling it apart from a legitimate `0` is
	 * what lets the caller reject it.
	 *
	 * @param string $type  Declared setting type.
	 * @param mixed  $value Raw value.
	 * @return string
	 */
	public static function normalize( string $type, $value ): string {
		if ( is_bool( $value ) ) {
			return self::to_stored_bool( $value );
		}

		$value = (string) $value;

		if ( '' === $value ) {
			return '';
		}

		if ( self::is_literal( $type, $value ) ) {
			return $value;
		}

		if ( self::BOOLEAN === self::parse( $type )['primitive'] ) {
			return self::to_stored_bool( self::to_bool( $value ) );
		}

		return $value;
	}

	/**
	 * Literals are kept verbatim, so `'page'`, `'session'` or `'forced'` reach the
	 * SDK as strings.
	 *
	 * @param string $type  Declared setting type.
	 * @param mixed  $value Stored value.
	 * @return mixed
	 */
	public static function cast( string $type, $value ) {
		if ( self::is_literal( $type, $value ) ) {
			return $value;
		}

		switch ( self::parse( $type )['primitive'] ) {
			case self::BOOLEAN:
				return self::to_bool( $value );

			case self::NUMBER:
				return self::to_number( $value );

			case self::STRING_LIST:
				return self::to_string_list( $value );

			default:
				return (string) $value;
		}
	}

	/**
	 * @param string $type Declared setting type.
	 * @return array{primitive:string|null,literals:array<int,string>}
	 */
	private static function split( string $type ): array {
		$primitive = null;
		$literals  = array();

		foreach ( explode( '|', $type ) as $member ) {
			$member = trim( $member );

			if ( 1 === preg_match( "/^'(.*)'$/", $member, $matches ) ) {
				$literals[] = $matches[1];
				continue;
			}

			if ( null === $primitive && in_array( $member, self::PRIMITIVES, true ) ) {
				$primitive = $member;
			}
		}

		return array(
			'primitive' => $primitive,
			'literals'  => $literals,
		);
	}

	/**
	 * An unresolved primitive accepts anything: the merchant edits it as text.
	 *
	 * @param string $type  Declared setting type.
	 * @param string $value Value to check.
	 * @return bool
	 */
	private static function matches_primitive( string $type, string $value ): bool {
		switch ( self::parse( $type )['primitive'] ) {
			case self::BOOLEAN:
				return self::reads_as_bool( $value );

			case self::NUMBER:
				return is_numeric( $value );

			case self::STRING_LIST:
				return array() !== self::to_string_list( $value );

			default:
				return true;
		}
	}

	/** Accepts exactly what to_bool() understands. */
	private static function reads_as_bool( string $value ): bool {
		return null !== filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	}

	private static function to_stored_bool( bool $value ): string {
		return $value ? '1' : '0';
	}

	/**
	 * @param mixed $value Value to cast.
	 * @return bool
	 */
	private static function to_bool( $value ): bool {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
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
	 * Trims the members and drops the empty ones.
	 *
	 * @param mixed $value Value to cast.
	 * @return array<int, string>
	 */
	private static function to_string_list( $value ): array {
		$items = is_array( $value ) ? $value : explode( ',', (string) $value );
		$items = array_map( 'trim', $items );

		return array_values( array_filter( $items, static fn( $item ) => '' !== $item ) );
	}
}
