<?php
/**
 * Abstract Cookie Builder
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

use Axeptio\Plugin\Contracts\Cookie_Interface;

/**
 * Base class for all cookie builders.
 */
abstract class Abstract_Cookie_Builder {

	/**
	 * Cookie instance being built.
	 *
	 * @var Cookie_Interface
	 */
	protected Cookie_Interface $cookie;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the builder with a fresh cookie instance.
	 *
	 * @return void
	 */
	abstract public function init(): void;

	/**
	 * Set cookie expiry time.
	 *
	 * @param int $expiry Expiry time in seconds.
	 * @return void
	 */
	public function set_expiry( int $expiry ): void {
		$this->cookie->set_expiry( $expiry );
	}

	/**
	 * Set cookie path.
	 *
	 * @param string $path Cookie path.
	 * @return void
	 */
	public function set_path( string $path ): void {
		$this->cookie->set_path( $path );
	}

	/**
	 * Set cookie secure flag.
	 *
	 * @param bool $secure Secure flag.
	 * @return void
	 */
	public function set_secure( bool $secure ): void {
		$this->cookie->set_secure( $secure );
	}

	/**
	 * Set cookie HTTP only flag.
	 *
	 * @param bool $httponly HTTP only flag.
	 * @return void
	 */
	public function set_httponly( bool $httponly ): void {
		$this->cookie->set_httponly( $httponly );
	}

	/**
	 * Set cookie SameSite attribute.
	 *
	 * @param string $same_site SameSite value.
	 * @return void
	 */
	public function set_same_site( string $same_site ): void {
		$this->cookie->set_same_site( $same_site );
	}
}
