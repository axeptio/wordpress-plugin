<?php
/**
 * Axeptio Cookies
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

use Axeptio\Plugin\Contracts\Cookie_Interface;

/**
 * Main Axeptio consent cookie containing user token and preferences.
 */
class Axeptio_Cookies extends Abstract_Cookie implements Cookie_Interface {

	/**
	 * Token data key.
	 *
	 * @var string
	 */
	public const TOKEN_DATA = '$$token';

	/**
	 * Date data key.
	 *
	 * @var string
	 */
	public const DATE_DATA = '$$date';

	/**
	 * Cookies version data key.
	 *
	 * @var string
	 */
	public const COOKIES_VERSION_DATA = '$$cookiesVersion';

	/**
	 * Complete data key.
	 *
	 * @var string
	 */
	public const COMPLETE_DATA = '$$completed';

	/**
	 * Cookie name.
	 *
	 * @var string
	 */
	protected const COOKIE_NAME = 'axeptio_cookies';

	/**
	 * User token.
	 *
	 * @var string
	 */
	protected string $user_token = '';

	/**
	 * User preferences.
	 *
	 * @var array<string, mixed>
	 */
	protected array $user_preferences = array();

	/**
	 * Get user token.
	 *
	 * @return string User token.
	 */
	public function get_user_token(): string {
		return $this->user_token;
	}

	/**
	 * Set user token.
	 *
	 * @param string $user_token User token.
	 * @return void
	 */
	public function set_user_token( string $user_token ): void {
		$this->user_token = $user_token;
	}

	/**
	 * Get user preferences.
	 *
	 * @return array<string, mixed> User preferences.
	 */
	public function get_user_preferences(): array {
		return $this->user_preferences;
	}

	/**
	 * Set user preferences.
	 *
	 * @param array<string, mixed> $user_preferences User preferences.
	 * @return void
	 */
	public function set_user_preferences( array $user_preferences ): void {
		$this->user_preferences = $user_preferences;
	}

	/**
	 * Get cookie data as JSON string.
	 *
	 * @return string JSON encoded cookie data.
	 */
	public function get_cookie_data(): string {
		$default_data = array(
			self::TOKEN_DATA           => $this->get_user_token(),
			self::DATE_DATA            => gmdate( 'c' ),
			self::COOKIES_VERSION_DATA => true,
			self::COMPLETE_DATA        => true,
		);

		return (string) wp_json_encode( array_merge( $default_data, $this->get_user_preferences() ) );
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
