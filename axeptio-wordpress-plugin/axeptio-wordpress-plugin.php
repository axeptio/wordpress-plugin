<?php

/**
 * @package AxeptioWPPlugin
 */

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


/*
    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

    Copyright Axeptio.
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
    Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_xpwp_plugin');

function deactivate_xpwp_plugin()
{
    Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_xpwp_plugin');

// Register Services
if (class_exists('Inc\\Init')) {
    Inc\Init::register_services();
}
