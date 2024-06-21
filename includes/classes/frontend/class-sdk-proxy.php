<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Frontend;

use Axeptio\Plugin\Module;

class Sdk_Proxy extends Module
{

	const CACHETIME = DAY_IN_SECONDS;
	const CACHEFILE = 'axeptio-sdk.js';
	const CACHEDIR = 'axeptio';

	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register()
	{
		return true;
	}

	/**
	 * Registering the admin page.
	 *
	 * @return void
	 */
	public function register()
	{
		add_action('init', array( $this, 'add_rewrite_rules' ) );
		add_filter('query_vars', array( $this, 'add_query_vars' ) );
		add_action('template_redirect', array( $this,'proxy_cmp_js' ) );
		add_filter('redirect_canonical', array( $this,'remove_trailing_slash' ), 20, 2 );
	}

	public function remove_trailing_slash(string $redirect_url) {
		if (!get_query_var('proxy_axeptio_sdk')) {
			return $redirect_url;
		}
		return rtrim($redirect_url, '/');
	}

	public function add_rewrite_rules() {
		add_rewrite_rule('^cmp\.js$', 'index.php?proxy_axeptio_sdk=1', 'top');
	}

	function add_query_vars($vars) {
		$vars[] = 'proxy_axeptio_sdk';
		return $vars;
	}

	function proxy_cmp_js() {
		if (!get_query_var('proxy_axeptio_sdk')) {
			return;
		}

		$file_path = wp_upload_dir()['basedir'] . '/' . self::CACHEDIR . '/' . self::CACHEFILE;

		// Create the axeptio directory if it doesn't exist
		if (!file_exists(self::CACHEDIR)) {
			wp_mkdir_p(self::CACHEDIR);
			// Set the directory permissions
			chmod(self::CACHEDIR, 0755);
		}

		// Set the correct headers
		header('Content-Type: application/javascript');
		header('Cache-Control: public, max-age=' . self::CACHETIME);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + self::CACHETIME) . ' GMT');

		if (file_exists($file_path) && (time() - filemtime($file_path)) < self::CACHETIME) {
			// Serve the cached file
			readfile($file_path);
			exit;
		}

		// URL of the external file
		$external_url = 'https://static.axept.io/sdk.js';

		// Get the file content
		$response = wp_remote_get($external_url);

		if (is_wp_error($response)) {
			wp_die('Error fetching the file.');
		}

		$body = wp_remote_retrieve_body($response);


		// Save the file locally
		file_put_contents($file_path, $body);

		echo $body;
		exit;
	}
}
