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
				'text'  => __( 'None', 'axeptio-sdk-integration' ),
			),
			array(
				'value' => 'all',
				'text'  => __( 'All shortcode tags', 'axeptio-sdk-integration' ),
			),
			array(
				'value' => 'blacklist',
				'text'  => __( 'Only the following', 'axeptio-sdk-integration' ),
			),
			array(
				'value' => 'whitelist',
				'text'  => __( 'Only those other than', 'axeptio-sdk-integration' ),
			),
		);
		if ( 'all' !== $configuration_id ) {
			array_unshift(
				$common,
				array(
					'value' => 'inherit',
					'text'  => __( 'Inherited from defaults', 'axeptio-sdk-integration' ),
				)
			);
		}

		$recommended_settings = Recommended_Plugin_Settings::find( $plugin_id );

		if ( $recommended_settings && isset( $recommended_settings['shortcode_tags_mode'] ) ) {
			array_unshift(
				$common,
				array(
					'value' => 'recommended',
					'text'  => __( 'Recommended by Axeptio', 'axeptio-sdk-integration' ),
				)
			);
		}
		return $common;
	}
}
