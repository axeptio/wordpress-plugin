<?php
/**
 * Plugin Name: axeptio-wordpress-plugin
 * Plugin URI: https://www.axeptio.eu/
 * Description: Proof Of Concept Axeptio
 * Version: 0.1
 * Author: Romain Bessuges-Meusy
 * Author URI: https://www.axeptio.eu/
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

if (is_admin()) {
	register_activation_hook(__file__, [Admin::class, 'activate' ]);
	register_deactivation_hook(__file__, [Admin::class, 'deactivate']);
	new Admin();
} else {
	Plugin::instance();
}