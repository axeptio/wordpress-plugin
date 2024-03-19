<?php
namespace Axeptio\Plugin\Third_Party;

use Axeptio\Plugin\Module;

class Wp_Rocket extends Module {

	const COOKIE_KEY = 'axeptio_cache_identifier';

	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register the WP Rocket filter.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'rocket_cache_dynamic_cookies', array( $this, 'add_axeptio_to_dynamic_cookies' ) );
	}

	/**
	 * Add the Axeptio cookie to the dynamic cookies.
	 *
	 * @param array $cookies The WP Rocket dynamic cookie list.
	 * @return array
	 */
	public function add_axeptio_to_dynamic_cookies( $cookies ) {
		$cookies[] = self::COOKIE_KEY;
		return $cookies;
	}


}
