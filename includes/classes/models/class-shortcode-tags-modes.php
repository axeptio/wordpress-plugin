<?php
/**
 * Plugins Model
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Models;

class Shortcode_Tags_Modes {
	/**
	 * Get shortcode tags mode list.
	 *
	 * @param string $configuration_id Configuration ID.
	 * @param string $plugin_id Plugin ID.
	 * @return array[]
	 */
	public static function all( $configuration_id, $plugin_id ) {
		$common = array(
			array(
				'value' => 'none',
				'text'  => __( 'None', 'axeptio-wordpress-plugin' ),
			),
			array(
				'value' => 'all',
				'text'  => __( 'All shortcode tags', 'axeptio-wordpress-plugin' ),
			),
			array(
				'value' => 'blacklist',
				'text'  => __( 'Only the following', 'axeptio-wordpress-plugin' ),
			),
			array(
				'value' => 'whitelist',
				'text'  => __( 'Only those other than', 'axeptio-wordpress-plugin' ),
			),
		);
		if ( 'all' !== $configuration_id ) {
			array_unshift(
				$common,
				array(
					'value' => 'inherit',
					'text'  => __( 'Inherited from defaults', 'axeptio-wordpress-plugin' ),
				)
			);
		}

		$recommended_settings = Recommended_Plugin_Settings::find( $plugin_id );

		if ( $recommended_settings && isset( $recommended_settings['shortcode_tags_mode'] ) ) {
			array_unshift(
				$common,
				array(
					'value' => 'recommended',
					'text'  => __( 'Recommended by Axeptio', 'axeptio-wordpress-plugin' ),
				)
			);
		}
		return $common;
	}
}
