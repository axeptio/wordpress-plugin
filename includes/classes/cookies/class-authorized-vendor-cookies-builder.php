<?php
/**
 * Authorized Vendor Cookies Builder
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

use Axeptio\Plugin\Contracts\Cookie_Builder_Interface;

/**
 * Builder for Authorized_Vendor_Cookies instances.
 */
class Authorized_Vendor_Cookies_Builder extends Abstract_Cookie_Builder implements Cookie_Builder_Interface {

	/**
	 * Cookie instance being built.
	 *
	 * @var Authorized_Vendor_Cookies
	 */
	protected Authorized_Vendor_Cookies $cookie;

	/**
	 * Initialize the builder with a fresh cookie instance.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->cookie = new Authorized_Vendor_Cookies();
	}

	/**
	 * Set user preferences (authorized vendor IDs).
	 *
	 * @param array<int, string> $user_preferences User preferences.
	 * @return void
	 */
	public function set_user_preferences( array $user_preferences ): void {
		$this->cookie->set_user_preferences( $user_preferences );
	}

	/**
	 * Create and return the built cookie, then reset for next build.
	 *
	 * @return Authorized_Vendor_Cookies The built cookie instance.
	 */
	public function create(): Authorized_Vendor_Cookies {
		$result = $this->cookie;
		$this->init();

		return $result;
	}
}
