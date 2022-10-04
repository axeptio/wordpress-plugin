<?php
/**
 * @package AxeptioWordpressPlugin
 */

namespace IncludeAxeptioWordpressPlugin\Base;

class Activate{
	public static function activate() {
		flush_rewrite_rules();
		add_action('plugins_loaded', 'loadLanguageFiles');
	}

	function loadLanguageFiles() {
		load_plugin_textdomain( 'axeptio-wordpress-plugin', false, $this->plugin_url . '../languages/');
	}
}