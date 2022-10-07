<?php
/**
 * @package AxeptioWPPlugin
 */

namespace Axpetio\SDKPlugin\Inc;

final class Init
{
    public static function get_services(){
        return array(
            \Axpetio\SDKPlugin\Inc\Pages\Admin::class,
            \Axpetio\SDKPlugin\Inc\Base\Enqueue::class
        );
    }

	public static function register_services(){
        foreach(self::get_services() as $class){
            $service = self::instantiate($class);
            if (method_exists($service, 'register')){
                $service->register();
            }
        }
    }

    private static function instantiate($class){
        $service = new $class();
        return $service;
    }
}