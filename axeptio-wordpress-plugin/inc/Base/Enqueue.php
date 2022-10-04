<?php

/**
 * @package AxeptioWPPlugin
 */
namespace IncludeAxeptioWordpressPlugin\Base;

use \IncludeAxeptioWordpressPlugin\Base\BaseController;
class Enqueue extends BaseController
{
    public function register(){
        add_action('wp_enqueue_scripts', [$this, 'registerSdkJs']);
        add_action( 'admin_enqueue_scripts', [$this, 'registerAdminJs']);
    }

    /**
     * Add sdk js to Wordpress
     */
    public function registerSdkJs() {
        $escaped_active = esc_attr( get_option( 'xpwp_sdk_active' ) );
        $escaped_clientId = esc_attr( get_option( 'xpwp_client_id' ) );
        $escaped_version = esc_attr( get_option( 'xpwp_version' ) );

        if ($escaped_active) {
            wp_enqueue_script('sdk-script', $this->plugin_url . '../assets/script.js');
            wp_localize_script('sdk-script', 'sdk_script_vars', array(
                    'clientId' => $escaped_clientId,
                    'version' => $escaped_version
                )
            );
        }
    }

    /**
     * Add admin logic js to plugin
     */
    public function registerAdminJs() {
        $escaped_clientId = esc_attr( get_option( 'xpwp_client_id' ) );
        $escaped_version = esc_attr( get_option( 'xpwp_version' ) );

        wp_enqueue_script('sdk-script', $this->plugin_url . '../assets/adminScript.js');
        wp_localize_script('sdk-script', 'sdk_script_vars', array(
            'clientId' => $escaped_clientId,
            'version' => $escaped_version
        )
    );
    }
}