<?php

/**
 * @package AxeptioWPPlugin
 */

namespace Axpetio\SDKPlugin;

/*
    Plugin Name: Axeptio SDK Integration
    Plugin URI: https://www.axeptio.eu/fr/home
    Description: Integrate Axeptio SDK to your Wordpress Website
    Version: 1.0.1
    Author: axeptio
    Licence: GPLv2 or later
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
function activate_xpwp_plugin()
{
    \Axpetio\SDKPlugin\Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\activate_xpwp_plugin');

function deactivate_xpwp_plugin()
{
    \Axpetio\SDKPlugin\Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivate_xpwp_plugin');

// Register Services
if (class_exists('\\Axpetio\\SDKPlugin\\Inc\\Init')) {
    \Axpetio\SDKPlugin\Inc\Init::register_services();
}
