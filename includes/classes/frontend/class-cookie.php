<?php
/**
 * Cookie Saver
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Frontend;

use Axeptio\Plugin\Module;
use Axeptio\SDK\Cookies\AxeptioCookieManager;
use Axeptio\SDK\Cookies\Builder\AllVendorCookiesBuilder;
use Axeptio\SDK\Cookies\Builder\AuthorizedVendorCookiesBuilder;
use Axeptio\SDK\Cookies\Builder\AxeptioCookiesBuilder;
use Axeptio\SDK\Cookies\Exception\UndefinedCookie;

class Cookie extends Module {
	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register(): bool
	{
		return true;
	}

	/**
	 * Registering the cookie actions
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'axeptio/ajax_nopriv_set_cookie', array( $this, 'set_cookie' ) );
		add_action( 'axeptio/ajax_set_cookie', array( $this, 'set_cookie' ) );
	}

	/**
	 * Set user cookie.
	 *
	 * @return void
	 * @throws UndefinedCookie
	 */
	public function set_cookie() {
		$input_json = stripslashes($_POST['userPreferencesManager']);
		$input = json_decode($input_json); // Convert JSON to array


		if (!isset($input->choices)) {
			wp_send_json_error();
		}

		$userToken = $input->choices->{'$$token'};

		$user_preferences = [];
		$all_vendors = [];

		foreach ((array)$input->choices as $key => $value) {
			if (str_contains($key, '$$') !== false) {
				continue;
			}
			if ($value) {
				$user_preferences[] = $key;
			}
			$all_vendors[] = $key;
		}

		$cookie_manager = new AxeptioCookieManager();

		$axeptio_cookie_builder = new AxeptioCookiesBuilder();
		$axeptio_cookie_builder->setUserToken($userToken);
		$axeptio_cookie_builder->setUserPreferences($user_preferences);
		$axeptio_cookie_builder->setExpiry(172800);
		$axeptio_cookie = $axeptio_cookie_builder->create();

		$authorized_vendor_cookies_builder = new AuthorizedVendorCookiesBuilder();
		$authorized_vendor_cookies_builder->setUserPreferences($user_preferences);
		$authorized_vendor_cookies = $authorized_vendor_cookies_builder->create();

		$all_vendor_cookies_builder = new AllVendorCookiesBuilder();
		$all_vendor_cookies_builder->setVendors($all_vendors);
		$all_vendor_cookies = $all_vendor_cookies_builder->create();

		$cookie_manager->addAxeptioCookies($axeptio_cookie);
		$cookie_manager->addAuthorizedVendorCookies($authorized_vendor_cookies);
		$cookie_manager->addAllVendorCookies($all_vendor_cookies);
		$cookie_manager->set();

		wp_send_json_success();
	}
}
