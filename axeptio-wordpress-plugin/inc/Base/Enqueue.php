<?php

/**
 * @package AxeptioWPPlugin
 */
namespace Inc\Base;

use \Inc\Base\BaseController;
class Enqueue extends BaseController
{
    public function register(){
        add_action('wp_enqueue_scripts', [$this, 'registerSdkJs']);
        add_action( 'admin_enqueue_scripts', [$this, 'registerAdminJs']);
    }

    /**
     * Add sdk js to wordpress
     */
    public function registerSdkJs() {
        $active = esc_attr( get_option( 'xpwp_sdk_active' ) );
        $clientId = esc_attr( get_option( 'xpwp_client_id' ) );
        $version = esc_attr( get_option( 'xpwp_version' ) );

        if ($active) {
            wp_enqueue_script('sdk-script', $this->plugin_url . '../assets/script.js');
            wp_localize_script('sdk-script', 'sdk_script_vars', array(
                    'clientId' => $clientId,
                    'version' => $version
                )
            );
        }
    }

    /**
     * Add admin logic js to plaugin
     */
    public function registerAdminJs() {
        $clientId = esc_attr( get_option( 'xpwp_client_id' ) );
        $version = esc_attr( get_option( 'xpwp_version' ) );

        wp_enqueue_script('sdk-script', $this->plugin_url . '../assets/adminScript.js');
        wp_localize_script('sdk-script', 'sdk_script_vars', array(
            'clientId' => $clientId,
            'version' => $version
        )
    );
    }
}