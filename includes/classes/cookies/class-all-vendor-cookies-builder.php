<?php
/**
 * All Vendor Cookies Builder
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

use Axeptio\Plugin\Contracts\Cookie_Builder_Interface;

/**
 * Builder for All_Vendor_Cookies instances.
 */
class All_Vendor_Cookies_Builder extends Abstract_Cookie_Builder implements Cookie_Builder_Interface {

	/**
	 * Cookie instance being built.
	 *
	 * @var All_Vendor_Cookies
	 */
	protected All_Vendor_Cookies $cookie;

	/**
	 * Initialize the builder with a fresh cookie instance.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->cookie = new All_Vendor_Cookies();
	}

	/**
	 * Set vendors list.
	 *
	 * @param array<int, string> $vendors Vendors list.
	 * @return void
	 */
	public function set_vendors( array $vendors ): void {
		$this->cookie->set_vendors( $vendors );
	}

	/**
	 * Create and return the built cookie, then reset for next build.
	 *
	 * @return All_Vendor_Cookies The built cookie instance.
	 */
	public function create(): All_Vendor_Cookies {
		$result = $this->cookie;
		$this->init();

		return $result;
	}
}
