<?php
/**
 * Abstract Cookie
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

/**
 * Base class for all cookie implementations.
 */
abstract class Abstract_Cookie {

	/**
	 * Cookie option key for expires.
	 *
	 * @var string
	 */
	public const EXPIRES_OPTION_KEY = 'expires';

	/**
	 * Cookie option key for path.
	 *
	 * @var string
	 */
	public const PATH_OPTION_KEY = 'path';

	/**
	 * Cookie option key for secure flag.
	 *
	 * @var string
	 */
	public const SECURE_OPTION_KEY = 'secure';

	/**
	 * Cookie option key for httponly flag.
	 *
	 * @var string
	 */
	public const HTTPONLY_OPTION_KEY = 'httponly';

	/**
	 * Cookie option key for samesite attribute.
	 *
	 * @var string
	 */
	public const SAMESITE_OPTION_KEY = 'samesite';

	/**
	 * Cookie expiry time in seconds.
	 *
	 * @var int
	 */
	protected int $expiry = 86400;

	/**
	 * Cookie path.
	 *
	 * @var string
	 */
	protected string $path = '/';

	/**
	 * Cookie secure flag.
	 *
	 * @var bool
	 */
	protected bool $secure = true;

	/**
	 * Cookie HTTP only flag.
	 *
	 * @var bool
	 */
	protected bool $httponly = true;

	/**
	 * Cookie SameSite attribute.
	 *
	 * @var string
	 */
	protected string $same_site = 'Strict';

	/**
	 * Get the cookie name.
	 *
	 * @return string Cookie name.
	 */
	abstract public function get_cookie_name(): string;

	/**
	 * Get the cookie data.
	 *
	 * @return string Cookie data.
	 */
	abstract public function get_cookie_data(): string;

	/**
	 * Set the cookie.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function set(): bool {
		// @phpstan-ignore-next-line
		return setcookie( $this->get_cookie_name(), $this->get_cookie_data(), $this->get_cookie_options() );
	}

	/**
	 * Get cookie options array for setcookie().
	 *
	 * @return array<string, mixed> Cookie options.
	 */
	public function get_cookie_options(): array {
		return array(
			self::EXPIRES_OPTION_KEY  => time() + $this->get_expiry(),
			self::PATH_OPTION_KEY     => $this->get_path(),
			self::SECURE_OPTION_KEY   => $this->is_secure(),
			self::HTTPONLY_OPTION_KEY => $this->is_httponly(),
			self::SAMESITE_OPTION_KEY => $this->get_same_site(),
		);
	}

	/**
	 * Get cookie expiry time in seconds.
	 *
	 * @return int Expiry time in seconds.
	 */
	public function get_expiry(): int {
		return $this->expiry;
	}

	/**
	 * Set cookie expiry time.
	 *
	 * @param int $expiry Expiry time in seconds.
	 * @return void
	 */
	public function set_expiry( int $expiry ): void {
		$this->expiry = $expiry;
	}

	/**
	 * Get cookie path.
	 *
	 * @return string Cookie path.
	 */
	public function get_path(): string {
		return $this->path;
	}

	/**
	 * Set cookie path.
	 *
	 * @param string $path Cookie path.
	 * @return void
	 */
	public function set_path( string $path ): void {
		$this->path = $path;
	}

	/**
	 * Check if cookie is secure.
	 *
	 * @return bool True if secure.
	 */
	public function is_secure(): bool {
		return $this->secure;
	}

	/**
	 * Set cookie secure flag.
	 *
	 * @param bool $secure Secure flag.
	 * @return void
	 */
	public function set_secure( bool $secure ): void {
		$this->secure = $secure;
	}

	/**
	 * Check if cookie is HTTP only.
	 *
	 * @return bool True if HTTP only.
	 */
	public function is_httponly(): bool {
		return $this->httponly;
	}

	/**
	 * Set cookie HTTP only flag.
	 *
	 * @param bool $httponly HTTP only flag.
	 * @return void
	 */
	public function set_httponly( bool $httponly ): void {
		$this->httponly = $httponly;
	}

	/**
	 * Get cookie SameSite attribute.
	 *
	 * @return string SameSite value.
	 */
	public function get_same_site(): string {
		return $this->same_site;
	}

	/**
	 * Set cookie SameSite attribute.
	 *
	 * @param string $same_site SameSite value.
	 * @return void
	 */
	public function set_same_site( string $same_site ): void {
		$this->same_site = $same_site;
	}
}
