<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Frontend;

use Axeptio\Plugin\Models\Settings;
use Axeptio\Plugin\Module;

class Sdk_Proxy extends Module
{
	/**
	 * Constants for cache time, file name, and directory.
	 */
	const CACHE_TIME = DAY_IN_SECONDS;
	const CACHE_FILE = 'axeptio-sdk.js';
	const CACHE_DIR = 'axeptio';

	/**
	 * Check if the module can be registered.
	 *
	 * @return bool True if the module can be registered, false otherwise.
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register the module's actions and filters.
	 */
	public function register() {
		add_action('init', [$this, 'add_rewrite_rules']);
		add_filter('query_vars', [$this, 'add_query_vars']);
		add_action('template_redirect', [$this, 'proxy_cmp_js']);
		add_filter('redirect_canonical', [$this, 'remove_trailing_slash'], 20, 2);
		add_action('update_option_axeptio_settings', [$this, 'set_axeptio_settings'], 20, 2);
	}

	/**
	 * Set the axeptio settings when they are updated.
	 *
	 * @param mixed $old_value The old value of the settings.
	 * @param mixed $new_value The new value of the settings.
	 */
	public function set_axeptio_settings($old_value, $new_value) {
		if (isset($new_value['proxy_sdk']) && $new_value['proxy_sdk']) {
			$proxy_file = sanitize_title(wp_generate_password(12, false));
			update_option('axeptio/sdk_proxy_key', $proxy_file);
		}
		update_option('axeptio/need_flush', '1');
	}

	/**
	 * Remove trailing slash from the redirect URL if the 'proxy_axeptio_sdk' query var is present.
	 *
	 * @param string $redirect_url The URL to redirect to.
	 * @param string $requested_url The URL requested by the user.
	 * @return string The modified redirect URL.
	 */
	public function remove_trailing_slash($redirect_url, $requested_url) {
		if (!get_query_var('proxy_axeptio_sdk')) {
			return $redirect_url;
		}
		return rtrim($redirect_url, '/');
	}

	/**
	 * Add rewrite rules for the SDK proxy.
	 */
	public function add_rewrite_rules() {
		if (Settings::get_option('proxy_sdk', false)) {
			$sdk = $this->get_sdk_proxy_key();
			add_rewrite_rule('^' . $sdk . '\.js$', 'index.php?proxy_axeptio_sdk=1', 'top');
		}

		if (get_option('axeptio/need_flush', '0')) {
			flush_rewrite_rules();
			update_option('axeptio/need_flush', '0');
		}
	}

	/**
	 * Get the SDK proxy key from the options.
	 *
	 * @return string The SDK proxy key.
	 */
	protected function get_sdk_proxy_key() {
		return get_option('axeptio/sdk_proxy_key');
	}

	/**
	 * Add the 'proxy_axeptio_sdk' query var to the list of query vars.
	 *
	 * @param array $vars The list of query vars.
	 * @return array The updated list of query vars.
	 */
	public function add_query_vars($vars) {
		$vars[] = 'proxy_axeptio_sdk';
		return $vars;
	}

	/**
	 * Serve the SDK proxy file.
	 */
	public function proxy_cmp_js() {
		if (!get_query_var('proxy_axeptio_sdk')) {
			return;
		}

		$file_path = wp_upload_dir()['basedir'] . '/' . self::CACHE_DIR . '/' . self::CACHE_FILE;

		if (!is_dir(self::CACHE_DIR)) {
			wp_mkdir_p(self::CACHE_DIR);
			chmod(self::CACHE_DIR, 0755);
		}

		header('Content-Type: application/javascript');
		header('Cache-Control: public, max-age=' . self::CACHE_TIME);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + self::CACHE_TIME) . ' GMT');

		if (file_exists($file_path) && (time() - filemtime($file_path)) < self::CACHE_TIME) {
			readfile($file_path);
			exit;
		}

		$external_url = 'https://static.axept.io/sdk.js';
		$response = wp_remote_get($external_url);

		if (is_wp_error($response)) {
			wp_die('Error fetching the file.');
		}

		$body = wp_remote_retrieve_body($response);
		file_put_contents($file_path, $body);
		echo $body;
		exit;
	}
}
