<?php

use Axeptio\Plugin\Models\Setting_Type;

/**
 * Read the cases shared with the JavaScript resolver.
 *
 * @param string $group Case group.
 * @return array<int, array>
 */
function xpwp_type_cases( string $group ): array {
	$fixture = json_decode( (string) file_get_contents( __DIR__ . '/fixtures/setting-types.json' ), true );

	return $fixture[ $group ];
}

it(
	'splits a declared type into a primitive and its literals',
	function () {
		foreach ( xpwp_type_cases( 'parse' ) as $case ) {
			$resolved = Setting_Type::parse( $case['type'] );

			expect( $resolved['primitive'] )->toBe( $case['primitive'], "primitive of {$case['type']}" );
			expect( $resolved['literals'] )->toBe( $case['literals'], "literals of {$case['type']}" );
		}
	}
);

it(
	'rejects empty values and values that do not match their type',
	function () {
		foreach ( xpwp_type_cases( 'validate' ) as $case ) {
			expect( Setting_Type::validate( $case['type'], $case['value'] ) )
				->toBe( $case['valid'], "{$case['type']} with '{$case['value']}'" );
		}
	}
);

it(
	'unifies boolean notations without swallowing an empty value',
	function () {
		foreach ( xpwp_type_cases( 'normalize' ) as $case ) {
			expect( Setting_Type::normalize( $case['type'], $case['value'] ) )
				->toBe( $case['expected'], "{$case['type']} with '{$case['value']}'" );
		}
	}
);

it(
	'casts union members to the type the SDK expects',
	function () {
		foreach ( xpwp_type_cases( 'cast' ) as $case ) {
			expect( Setting_Type::cast( $case['type'], $case['value'] ) )
				->toBe( $case['expected'], "{$case['type']} with '{$case['value']}'" );
		}
	}
);

it(
	'falls back to text for a type it does not know',
	function () {
		expect( Setting_Type::parse( 'Token' )['primitive'] )->toBeNull();
		expect( Setting_Type::cast( 'Token', 'abc' ) )->toBe( 'abc' );
		expect( Setting_Type::validate( 'Token', 'abc' ) )->toBeTrue();
	}
);
