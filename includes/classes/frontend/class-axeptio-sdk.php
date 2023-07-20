<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Frontend;

use Axeptio\Admin;
use Axeptio\Models\Axeptio_Steps;
use Axeptio\Models\Plugins;
use Axeptio\Models\Settings;
use Axeptio\Module;
use function Axeptio\get_cookie_domain;
use function Axeptio\get_sdk_settings;
use function Axeptio\script_url;
use function Axeptio\style_url;
use function Axeptio\Utility\get_asset_info;

class Axeptio_Sdk extends Module {

	const OPTION_JSON_COOKIE_NAME = 'axeptio_cookies';
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue the SDK scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$settings = $this->get_sdk_settings();

		if ( ! $settings ) {
			return;
		}

		wp_enqueue_style(
			'axeptio/main',
			style_url( 'frontend/main', 'frontend' ),
			array(),
			get_asset_info( 'shared', 'version' ),
		);

		$cookies_version = Settings::get_option( 'version', false );
		$cookies_version = '' === $cookies_version ? 'all' : $cookies_version;

		$wordpress_vendors = array_values(
			array_filter(
				array_map(
					function ( $plugin_configuration ) use ( $cookies_version ) {

						$configuration = 'all' !== $cookies_version && isset( $plugin_configuration['Metas']['Merged'] ) ? $plugin_configuration['Metas']['Merged'] : $plugin_configuration['Metas'];

						if ( ! isset( $configuration['enabled'] ) || ! (bool) $configuration['enabled'] ) {
								return false;
						}

						return array(
							'name'             => "wp_{$configuration['plugin']}" ?? '',
							'title'            => isset( $configuration['vendor_title'] ) && '' !== $configuration['vendor_title'] ? $configuration['vendor_title'] : $plugin_configuration['Name'],
							'shortDescription' => isset( $configuration['vendor_shortDescription'] ) && '' !== $configuration['vendor_shortDescription'] ? wp_strip_all_tags( $configuration['vendor_shortDescription'] ) : $plugin_configuration['Description'],
							'longDescription'  => wp_strip_all_tags( $configuration['vendor_longDescription'] ?? '' ),
							'policyUrl'        => isset( $configuration['vendor_policyUrl'] ) && '' !== $configuration['vendor_policyUrl'] ? $configuration['vendor_policyUrl'] : $plugin_configuration['PluginURI'],
							// TODO: Vendor Domain.
							'domain'           => $configuration['vendor_domain'] ?? '',
							'image'            => '' === $configuration['vendor_image'] && isset( $configuration['Merged']['vendor_image'] ) ? $configuration['Merged']['vendor_image'] : $configuration['vendor_image'],
							'type'             => 'wordpress plugin',
							'step'             => $configuration['cookie_widget_step'] ?? 'wordpress',
						);
					},
					Plugins::all( $cookies_version )
				)
			)
		);

		wp_enqueue_script(
			'axeptio/sdk-script',
			script_url( 'frontend/axeptio', 'frontend' ),
			array(),
			XPWP_VERSION,
			true
		);
		wp_localize_script( 'axeptio/sdk-script', 'Axeptio_SDK', $settings );
		wp_localize_script( 'axeptio/sdk-script', 'axeptioWordpressVendors', $wordpress_vendors );
		wp_localize_script( 'axeptio/sdk-script', 'axeptioWordpressSteps', Axeptio_Steps::all() );

		$inline_script = \Axeptio\get_template_part( 'frontend/sdk', array(), false );
		preg_match( '/<script[^>]*>(.*?)<\/script>/is', $inline_script, $matches );
		wp_add_inline_script( 'axeptio/sdk-script', $matches[1] ?? '' );
	}

	/**
	 * Retrieve the SDK settings.
	 *
	 * @return array|false Settings of the SDK.
	 */
	private function get_sdk_settings() {
		$sdk_active = (bool) Settings::get_option( 'sdk_active', false );
		$send_datas = (bool) Settings::get_option( 'send_datas', false );

		$client_id       = Settings::get_option( 'client_id', false );
		$cookies_version = Settings::get_option( 'version', false );

		if ( ! $sdk_active || ( ! $client_id && ! $cookies_version ) ) {
			return false;
		}

		return apply_filters(
			'axeptio/sdk_settings',
			array(
				'clientId'          => $client_id,
				'cookiesVersion'    => $cookies_version,
				'userCookiesDomain' => get_cookie_domain(),
				'sendDatas'         => $send_datas,
			)
		);
	}
}
