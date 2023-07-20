<?php
/**
 * Settings Model
 *
 * @package Axeptio
 */

namespace Axeptio\Models;

class Recommended_Plugin_Settings {
	/**
	 * Search for recommended plugin settings.
	 *
	 * @param string $plugin Plugin name.
	 * @return false|array
	 */
	public static function find( string $plugin ) {
		$data = self::get_contents( $plugin );
		return self::format_datas( $data );
	}

	/**
	 * Get plugin contents.
	 *
	 * @param string $plugin Plugin name.
	 * @return array
	 */
	public static function get_contents( string $plugin ) {
		$datas = self::maybe_pull_remote_datas();

		foreach ( $datas as &$data ) {
			if ( $data['states']['default']['data']['name'] === $plugin ) {
				return $data['states']['default']['data'];
			}
		}

		return array();
	}

	/**
	 * Maybe pull remote datas.
	 *
	 * @return array|false
	 */
	public static function maybe_pull_remote_datas() {
		global $wp_filesystem;

		static $data;

		if ( $data ) {
			return $data;
		}

		$cache_dir      = wp_upload_dir()['basedir'] . '/axeptio/';
		$cache_file     = $cache_dir . md5( 'axeptio-vendordb' ) . '.json';
		$cache_duration = 7 * DAY_IN_SECONDS;

		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();

		// Check if the cache file exists and is valid.
		if ( $wp_filesystem->exists( $cache_file ) && ( time() - filemtime( $cache_file ) < $cache_duration ) ) {
			$cache_data = $wp_filesystem->get_contents( $cache_file );
			$data       = json_decode( $cache_data, true );
			if ( null !== $data ) {
				return $data;
			}
		}

		$url      = 'https://api.axept.io/v1/vendors/plugin?data.platformId=648c6ff94aabe9248dac7eba';
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || empty( $data ) ) {
			return false;
		}

		if ( ! is_dir( $cache_dir ) ) {
			wp_mkdir_p( $cache_dir );
		}

		$wp_filesystem->put_contents( $cache_file, $body );

		return $data;
	}

	/**
	 * Purge the plugin settings cache for a specific plugin.
	 *
	 * @param string $plugin Plugin name.
	 */
	public static function purge_cache( string $plugin ) {
		$cache_dir  = wp_upload_dir()['basedir'] . '/axeptio/';
		$cache_file = $cache_dir . md5( $plugin ) . '.json';

		if ( file_exists( $cache_file ) ) {
			wp_delete_file( $cache_file );
		}
	}

	/**
	 * Format the data.
	 *
	 * @param array $datas Data to format.
	 * @return array|false
	 */
	public static function format_datas( array $datas ) {
		$settings = array(
			'wp_filter_mode'      => 'blacklist',
			'wp_filter_list'      => array(),
			'shortcode_tags_mode' => 'blacklist',
			'shortcode_tags_list' => array(),
		);

		if ( !isset( $datas['hooks'] ) ) {
			return false;
		}

		$hooks = $datas['hooks'];

		foreach ( $hooks as $hook ) {
			if ( 'filter' === $hook['type'] ) {
				$settings['wp_filter_list'][] = $hook['identifier'];
			} elseif ( 'shortcode' === $hook['type'] ) {
				$settings['shortcode_tags_list'][] = $hook['identifier'];
			}
		}

		return $settings;
	}
}
