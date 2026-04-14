<?php
/**
 * Cookie Builder Interface
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Contracts;

/**
 * Contract for cookie builder implementations.
 */
interface Cookie_Builder_Interface {

	/**
	 * Initialize the builder with a fresh cookie instance.
	 *
	 * @return void
	 */
	public function init(): void;
}
