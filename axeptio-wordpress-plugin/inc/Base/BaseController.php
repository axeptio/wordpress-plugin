<?php   

/**
 * @package AxeptioWPPlugin
 */

namespace Inc\Base;

class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $plugin_;
    public $text_domain;
    
    public function __construct(){
        $this->plugin_path = plugin_dir_path( dirname(__FILE__) );
        $this->plugin_url = plugin_dir_url( dirname(__FILE__) );
        $this->plugin = plugin_basename( dirname(__FILE__) ) . '/axeptio-wordpress-plugin.php';
        $this->text_domain = 'axeptio-wordpress-plugin';
    }
}