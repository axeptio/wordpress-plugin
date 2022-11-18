<?php
/**
 * Plugin Name: Axeptio Consent Management
 * Plugin URI: https://www.axeptio.eu/
 * Description: This plugin provides a complete integration of Axeptio and is capable of blocking 3rd party plugins.
 * Version: 2.0.0
 * Author: Axeptio
 * Author URI: https://www.axeptio.eu/
 * Text Domain: axeptio-wordpress-plugin
 **/

use Axeptio\Admin;
use Axeptio\Plugin;

/*
const WP_DEBUG = true;
const WP_DEBUG_DISPLAY = false;
const WP_DEBUG_LOG = true;
*/

const AXEPTIO_PLUGIN_FILE = __file__;

require_once __DIR__ . '/includes/Plugin.php';
require_once __DIR__ . '/includes/Admin.php';
require_once __DIR__ . '/includes/Plugin_Configurations_List_Table.php';
require_once __DIR__ . '/includes/Widget_Configurations_List_Table.php';

if (is_admin()) {
	register_activation_hook(__file__, [Admin::class, 'activate' ]);
	register_deactivation_hook(__file__, [Admin::class, 'deactivate']);
	new Admin();
} else {
	Plugin::instance();
}
