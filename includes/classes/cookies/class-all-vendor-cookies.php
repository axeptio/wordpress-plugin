<?php
/**
 * All Vendor Cookies
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

use Axeptio\Plugin\Contracts\Cookie_Interface;

/**
 * Cookie containing the list of all available vendors.
 */
class All_Vendor_Cookies extends Abstract_Cookie implements Cookie_Interface {

	/**
	 * Cookie name.
	 *
	 * @var string
	 */
	protected const COOKIE_NAME = 'axeptio_all_vendors';

	/**
	 * All vendors list.
	 *
	 * @var array<int, string>
	 */
	protected array $vendors = array();

	/**
	 * Set vendors list.
	 *
	 * @param array<int, string> $vendors Vendors list.
	 * @return void
	 */
	public function set_vendors( array $vendors ): void {
		$this->vendors = $vendors;
	}

	/**
	 * Get vendors list.
	 *
	 * @return array<int, string> Vendors list.
	 */
	public function get_vendors(): array {
		return $this->vendors;
	}

	/**
	 * Get cookie data as comma-separated vendor IDs.
	 *
	 * @return string Cookie data in format: ,vendor1,vendor2,
	 */
	public function get_cookie_data(): string {
		return sprintf( ',%s,', implode( ',', $this->get_vendors() ) );
	}

	/**
	 * Get the cookie name.
	 *
	 * @return string Cookie name.
	 */
	public function get_cookie_name(): string {
		return self::COOKIE_NAME;
	}
}
