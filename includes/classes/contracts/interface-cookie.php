<?php
/**
 * Cookie Interface
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Contracts;

/**
 * Contract for cookie implementations.
 */
interface Cookie_Interface {

	/**
	 * Set the cookie.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function set(): bool;

	/**
	 * Get cookie expiry time in seconds.
	 *
	 * @return int Expiry time in seconds.
	 */
	public function get_expiry(): int;

	/**
	 * Set cookie expiry time.
	 *
	 * @param int $expiry Expiry time in seconds.
	 * @return void
	 */
	public function set_expiry( int $expiry ): void;

	/**
	 * Get cookie path.
	 *
	 * @return string Cookie path.
	 */
	public function get_path(): string;

	/**
	 * Set cookie path.
	 *
	 * @param string $path Cookie path.
	 * @return void
	 */
	public function set_path( string $path ): void;

	/**
	 * Check if cookie is secure.
	 *
	 * @return bool True if secure.
	 */
	public function is_secure(): bool;

	/**
	 * Set cookie secure flag.
	 *
	 * @param bool $secure Secure flag.
	 * @return void
	 */
	public function set_secure( bool $secure ): void;

	/**
	 * Check if cookie is HTTP only.
	 *
	 * @return bool True if HTTP only.
	 */
	public function is_httponly(): bool;

	/**
	 * Set cookie HTTP only flag.
	 *
	 * @param bool $httponly HTTP only flag.
	 * @return void
	 */
	public function set_httponly( bool $httponly ): void;

	/**
	 * Get cookie SameSite attribute.
	 *
	 * @return string SameSite value.
	 */
	public function get_same_site(): string;

	/**
	 * Set cookie SameSite attribute.
	 *
	 * @param string $same_site SameSite value.
	 * @return void
	 */
	public function set_same_site( string $same_site ): void;
}
