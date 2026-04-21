<?php
/**
 * Authorized Vendor Cookies
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

use Axeptio\Plugin\Contracts\Cookie_Interface;

/**
 * Cookie containing the list of authorized vendors.
 */
class Authorized_Vendor_Cookies extends Abstract_Cookie implements Cookie_Interface {

	/**
	 * Cookie name.
	 *
	 * @var string
	 */
	protected const COOKIE_NAME = 'axeptio_authorized_vendors';

	/**
	 * User preferences (authorized vendor IDs).
	 *
	 * @var array<int, string>
	 */
	protected array $user_preferences = array();

	/**
	 * Get user preferences.
	 *
	 * @return array<int, string> User preferences.
	 */
	public function get_user_preferences(): array {
		return $this->user_preferences;
	}

	/**
	 * Set user preferences.
	 *
	 * @param array<int, string> $user_preferences User preferences.
	 * @return void
	 */
	public function set_user_preferences( array $user_preferences ): void {
		$this->user_preferences = $user_preferences;
	}

	/**
	 * Get cookie data as comma-separated vendor IDs.
	 *
	 * @return string Cookie data in format: ,vendor1,vendor2,
	 */
	public function get_cookie_data(): string {
		return sprintf( ',%s,', implode( ',', array_filter( $this->get_user_preferences() ) ) );
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
