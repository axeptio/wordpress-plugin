<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Compat;

defined( 'ABSPATH' ) || exit;

use Axeptio\Plugin\Module;

class AlpineJS_Wpkses extends Module {
	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Registering the admin page.
	 *
	 * @return void
	 */
	public function register() {
		add_filter(
			'wp_kses_allowed_html',
			static function ( $tags ) {
				$alpinized_tags    = array( 'div', 'section', 'template' );
				$alpine_directives = array(
					'x-if'                     => true,
					'x-show'                   => true,
					'x-cloak'                  => true,
					'x-collapse'               => true,
					'x-transition:enter'       => true,
					'x-transition:enter-start' => true,
					'x-transition:enter-end'   => true,
					'x-transition:leave'       => true,
					'x-transition:leave-start' => true,
					'x-transition:leave-end'   => true,
				);

				foreach ( $alpinized_tags as $alpinized_tag ) {
					if ( ! isset( $tags[ $alpinized_tag ] ) ) {
						continue;
					}
					$tags[ $alpinized_tag ] = array_merge( $alpine_directives, $tags[ $alpinized_tag ] );
				}
				return $tags;
			}
		);
	}
}
