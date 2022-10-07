<?php

/**
 * @package AxeptioWPPlugin
 */

namespace Axpetio\SDKPlugin;

/*
    Plugin Name: Axeptio SDK Integration
    Plugin URI: https://www.axeptio.eu/
    Description: Axeptio allows you to make your website compliant with GDPR.
    Version: 1.0.0
    Author: axeptio
    License: GPLv3
    License URI: https://www.gnu.org/licenses/gpl-3.0.html
    Text Domain: axeptio-wordpress-plugin
    Domain Path: /languages
    */

// Security Enhancement
if (!defined('ABSPATH')) {
    die;
}

defined('ABSPATH') or die();

if (!function_exists('add_action')) {
    die();
}

// Plugin Start
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// Procedural Activation
function axeptio_wordpress_plugin_activate_xpwp_plugin()
{
    \Axpetio\SDKPlugin\Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\axeptio_wordpress_plugin_activate_xpwp_plugin');

function axeptio_wordpress_plugin_deactivate_xpwp_plugin()
{
    \Axpetio\SDKPlugin\Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\axeptio_wordpress_plugin_deactivate_xpwp_plugin');

// Register Services
if (class_exists('\\Axpetio\\SDKPlugin\\Inc\\Init')) {
    \Axpetio\SDKPlugin\Inc\Init::register_services();
}
