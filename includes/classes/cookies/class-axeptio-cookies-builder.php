<?php
/**
 * Axeptio Cookies Builder
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

use Axeptio\Plugin\Contracts\Cookie_Builder_Interface;

/**
 * Builder for Axeptio_Cookies instances.
 */
class Axeptio_Cookies_Builder extends Abstract_Cookie_Builder implements Cookie_Builder_Interface {

	/**
	 * Cookie instance being built.
	 *
	 * @var Axeptio_Cookies
	 */
	protected Axeptio_Cookies $cookie;

	/**
	 * Initialize the builder with a fresh cookie instance.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->cookie = new Axeptio_Cookies();
	}

	/**
	 * Set user preferences.
	 *
	 * @param array<string, mixed> $user_preferences User preferences.
	 * @return void
	 */
	public function set_user_preferences( array $user_preferences ): void {
		$this->cookie->set_user_preferences( $user_preferences );
	}

	/**
	 * Set user token.
	 *
	 * @param string $user_token User token.
	 * @return void
	 */
	public function set_user_token( string $user_token ): void {
		$this->cookie->set_user_token( $user_token );
	}

	/**
	 * Create and return the built cookie, then reset for next build.
	 *
	 * @return Axeptio_Cookies The built cookie instance.
	 */
	public function create(): Axeptio_Cookies {
		$result = $this->cookie;
		$this->init();

		return $result;
	}
}
