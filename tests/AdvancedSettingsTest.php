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
