<?php
/**
 * Notice Model
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Notice {
	/**
	 * Set the timeout notice cookie.
	 *
	 * @return void
	 */
	public static function set_timeout() {
		setcookie( 'axeptio_timeout_notice', true, time() + 60 * 60 * 24 * 7, '/' );
	}

	/**
	 * Disable the notice for the current user by setting the user meta to true.
	 *
	 * @return void
	 */
	public static function disable() {
		$user = wp_get_current_user();
		if ( ! $user ) {
			return;
		}
		update_user_meta( $user->ID, 'axeptio_disable_notice', true );
	}

	/**
	 * Check if the notice is displayable.
	 *
	 * @return boolean
	 */
	public static function isDisplayable() {
		$user = wp_get_current_user();
		if ( ! $user ) {
			return false;
		}
		return ! get_user_meta( $user->ID, 'axeptio_disable_notice', true ) && ! isset( $_COOKIE['axeptio_timeout_notice'] );
	}
}
