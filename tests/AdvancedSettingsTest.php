<?php

use Axeptio\Plugin\Models\Advanced_Settings;

it(
	'casts each stored pair to its proper type',
	function () {
		\Mockery::mock( 'alias:Axeptio\Plugin\Models\Settings' )
			->shouldReceive( 'get_option' )
			->with( 'advanced_settings', array() )
			->andReturn(
				array(
					array(
						'property' => 'signalEssential',
						'type'     => 'boolean',
						'value'    => '1',
					),
					array(
						'property' => 'userCookiesSecure',
						'type'     => 'boolean',
						'value'    => '0',
					),
					array(
						'property' => 'jsonCookieName',
						'type'     => 'string',
						'value'    => 'my_cookie',
					),
					array(
						'property' => 'userCookiesDuration',
						'type'     => "number | 'page' | 'session'",
						'value'    => '365',
					),
					array(
						'property' => 'userCookiesDurationSession',
						'type'     => "number | 'page' | 'session'",
						'value'    => 'session',
					),
					array(
						'property' => 'userCrossCookiesDomain',
						'type'     => 'string[]',
						'value'    => '.example.com, .example.fr',
					),
					array(
						'property' => 'someUpdateOnly',
						'type'     => "boolean | 'update_only'",
						'value'    => 'update_only',
					),
				)
			);

		$typed = Advanced_Settings::get_typed();

		expect( $typed['signalEssential'] )->toBeTrue();
		expect( $typed['userCookiesSecure'] )->toBeFalse();
		expect( $typed['jsonCookieName'] )->toBe( 'my_cookie' );
		expect( $typed['userCookiesDuration'] )->toBe( 365 );
		expect( $typed['userCookiesDurationSession'] )->toBe( 'session' );
		expect( $typed['userCrossCookiesDomain'] )->toBe( array( '.example.com', '.example.fr' ) );
		expect( $typed['someUpdateOnly'] )->toBe( 'update_only' );
	}
);

it(
	'returns an empty map when no pairs are configured',
	function () {
		\Mockery::mock( 'alias:Axeptio\Plugin\Models\Settings' )
			->shouldReceive( 'get_option' )
			->with( 'advanced_settings', array() )
			->andReturn( array() );

		expect( Advanced_Settings::get_typed() )->toBe( array() );
	}
);

it(
	'keeps a union literal and a disabled boolean out of each other\'s way',
	function () {
		\Mockery::mock( 'alias:Axeptio\Plugin\Models\Settings' )
			->shouldReceive( 'get_option' )
			->with( 'advanced_settings', array() )
			->andReturn(
				array(
					array(
						'property' => 'compressUserCookie',
						'type'     => "boolean | 'forced'",
						'value'    => 'forced',
					),
					array(
						'property' => 'signalEssential',
						'type'     => 'boolean',
						'value'    => '0',
					),
				)
			);

		$typed = Advanced_Settings::get_typed();

		expect( $typed['compressUserCookie'] )->toBe( 'forced' );
		expect( $typed )->toHaveKey( 'signalEssential' );
		expect( $typed['signalEssential'] )->toBeFalse();
	}
);

it(
	'never injects a pair whose stored value is empty',
	function () {
		\Mockery::mock( 'alias:Axeptio\Plugin\Models\Settings' )
			->shouldReceive( 'get_option' )
			->with( 'advanced_settings', array() )
			->andReturn(
				array(
					array(
						'property' => 'jsonCookieName',
						'type'     => 'string',
						'value'    => '',
					),
					array(
						'property' => 'userCookiesDuration',
						'type'     => "number | 'page' | 'session'",
						'value'    => '',
					),
					array(
						'property' => 'compressUserCookie',
						'type'     => "boolean | 'forced'",
						'value'    => '',
					),
					array(
						'property' => 'mountClassName',
						'type'     => 'string',
						'value'    => 'my-class',
					),
				)
			);

		expect( Advanced_Settings::get_typed() )->toBe( array( 'mountClassName' => 'my-class' ) );
	}
);

it(
	'drops rows with an empty or mistyped value on save',
	function () {
		$sanitized = Advanced_Settings::sanitize(
			array(
				array(
					'property' => 'jsonCookieName',
					'type'     => 'string',
					'value'    => '',
				),
				array(
					'property' => 'userCookiesDuration',
					'type'     => "number | 'page' | 'session'",
					'value'    => 'not-a-number',
				),
				array(
					'property' => 'signalEssential',
					'type'     => 'boolean',
					'value'    => 'maybe',
				),
				array(
					'property' => 'clientId',
					'type'     => 'string',
					'value'    => 'plugin-managed',
				),
				array(
					'property' => '',
					'type'     => '',
					'value'    => '',
				),
			)
		);

		expect( $sanitized )->toBe( array() );
	}
);

it(
	'requires an absolute http(s) URL from a property named after one',
	function () {
		$rejected = Advanced_Settings::sanitize(
			array(
				array(
					'property' => 'apiUrl',
					'type'     => 'string',
					'value'    => '#test',
				),
				array(
					'property' => 'configUrl',
					'type'     => 'string',
					'value'    => 'javascript:alert(1)',
				),
				array(
					'property' => 'proxyBaseUrl',
					'type'     => 'string',
					'value'    => 'client.axept.io',
				),
				// Syntactically valid, but a dotless host is no public endpoint.
				array(
					'property' => 'postConsentUrl',
					'type'     => 'string',
					'value'    => 'https://test',
				),
			)
		);

		expect( $rejected )->toBe( array() );

		$accepted = Advanced_Settings::sanitize(
			array(
				array(
					'property' => 'apiUrl',
					'type'     => 'string',
					'value'    => 'https://api.axept.io/v1',
				),
				array(
					'property' => 'mountClassName',
					'type'     => 'string',
					'value'    => 'not-a-url',
				),
			)
		);

		expect( $accepted )->toHaveCount( 2 );
	}
);

it(
	'never reports a property another row managed to save',
	function () {
		$valid   = array( 'property' => 'apiUrl', 'type' => 'string', 'value' => 'https://api.axept.io/v1' );
		$invalid = array( 'property' => 'apiUrl', 'type' => 'string', 'value' => '#test' );

		foreach ( array( array( $invalid, $valid ), array( $valid, $invalid ) ) as $pairs ) {
			$sanitized = Advanced_Settings::sanitize( $pairs );

			expect( $sanitized )->toHaveCount( 1 );
			expect( $sanitized[0]['value'] )->toBe( 'https://api.axept.io/v1' );
			expect( Advanced_Settings::get_rejected() )->toBe( array() );
		}
	}
);

it(
	'keeps valid rows on save and unifies their boolean notation',
	function () {
		$sanitized = Advanced_Settings::sanitize(
			array(
				array(
					'property' => 'compressUserCookie',
					'type'     => 'boolean',
					'value'    => 'forced',
				),
				array(
					'property' => 'signalEssential',
					'type'     => 'boolean',
					'value'    => 'true',
				),
				array(
					'property' => 'userCookiesDuration',
					'type'     => "number | 'page' | 'session'",
					'value'    => 'session',
				),
			)
		);

		expect( $sanitized )->toHaveCount( 3 );

		// The type is re-read from the reference, so the stale `boolean` is corrected.
		expect( $sanitized[0]['type'] )->toBe( "boolean | 'forced'" );
		expect( $sanitized[0]['value'] )->toBe( 'forced' );
		expect( $sanitized[1]['value'] )->toBe( '1' );
		expect( $sanitized[2]['value'] )->toBe( 'session' );
	}
);
