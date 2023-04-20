<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function ( RectorConfig $rectorConfig ): void {
	$rectorConfig->paths(
		array(
			__DIR__ . '/includes',
		)
		);

	// register a single rule
	$rectorConfig->rule( InlineConstructorDefaultToPropertyRector::class );

	// define sets of rules
	$rectorConfig->sets( array( SetList::PHP_74 ) );
};
