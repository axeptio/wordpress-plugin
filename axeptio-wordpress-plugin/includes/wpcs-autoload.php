<?php
/**
 * Autoload functionality.
 *
 * @package Axeptio
 */

/**
 * Retrieve the file for the given class.
 *
 * @param string $class_name Name of the called class.
 * @param string $prefix Prefix of the called class.
 * @param string $folder Path of the autoloaded folder.
 * @return false|string Path to the file or false on failure.
 */
function xpwp_file_path( $class_name, $prefix, $folder ) {
	$class_name   = str_replace( $prefix, '', $class_name );
	$plugin_parts = explode( '\\', $class_name );
	$name         = array_pop( $plugin_parts );
	$name         = preg_match( '/^(Interface|Trait)/', $name )
		? $name . '.php'
		: 'class-' . $name . '.php';
	$local_path   = implode( DS, $plugin_parts ) . '/' . $name;
	$local_path   = strtolower( str_replace( array( '\\', '_' ), array( DS, '-' ), $local_path ) );

	$path = rtrim( $folder, DS ) . DS . $local_path;

	if ( file_exists( $path ) ) {
		return $path;
	}

	return false;
}

spl_autoload_register(
	function ( $class_name ) {
		$prefix   = 'Axeptio\\';
		$base_dir = __DIR__ . '/classes/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}

		$file = xpwp_file_path( $class_name, $prefix, $base_dir );

		if ( $file ) {
			require $file;
		} else {
			throw new Exception( $class_name, $prefix );
		}
	}
);
