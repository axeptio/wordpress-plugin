<?php
/**
 * Search Callback File Location.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Utils;

class Search_Callback_File_Location {
	private const BASE_DIRS = [
		WP_PLUGIN_DIR,
		WPMU_PLUGIN_DIR,
	];

	private const POSSIBLE_SUBFOLDERS = ['', 'src/', 'includes/', 'app/', 'core/', 'lib/'];

	/**
	 * Get the filename for the given callback with caching.
	 *
	 * @param mixed $callback The callback to find the file for.
	 * @param string|null $name The name of the callback.
	 * @param string|null $filter The filter name.
	 * @param int|null $priority The priority of the callback.
	 * @return string|null The filename if found, null otherwise.
	 * @throws \ReflectionException
	 */
	public static function get_filename($callback, string $name = null, string $filter = null, $priority = null): ?string
	{
		// If not cached, compute the filename
		$filename = self::find_file_for_callback($callback);

		if ($filename === null) {
			$reflection = self::get_reflection($callback);
			if ($reflection !== null) {
				$filename = $reflection->getFileName();
			}
		}

		return $filename;
	}


	/**
	 * Get the appropriate Reflection object for the given callback, handling errors silently.
	 *
	 * @param mixed $callback The callback to reflect on.
	 * @return \ReflectionFunctionAbstract|null
	 */
	public static function get_reflection($callback) {
		try {
			return self::create_reflection($callback);
		} catch (\ReflectionException $e) {
			error_log('Reflection error: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Attempts to create a reflection object based on the type and content of the callback.
	 *
	 * @param mixed $callback The callback to reflect on.
	 * @return \ReflectionFunctionAbstract
	 * @throws \ReflectionException If reflection creation fails.
	 */
	private static function create_reflection($callback) {
		if (is_string($callback) && function_exists($callback)) {
			return new \ReflectionFunction($callback);
		}
		if ($callback instanceof \Closure) {
			return new \ReflectionFunction($callback);
		}
		if (is_array($callback) && count($callback) === 2) {
			[$class, $method] = $callback;
			if ((is_string($class) && class_exists($class) && method_exists($class, $method)) ||
				(is_object($class) && method_exists($class, $method))) {
				return new \ReflectionMethod($class, $method);
			}
		}
		if (is_object($callback) && method_exists($callback, '__invoke')) {
			return new \ReflectionMethod($callback, '__invoke');
		}

		return null;
	}

	/**
	 * Attempt to find the file for a given callback.
	 *
	 * @param mixed $callback The callback to find the file for.
	 * @return string|null The filename if found, null otherwise.
	 */
	private static function find_file_for_callback($callback) {
		if (is_array($callback) && count($callback) === 2) {
			[$class, $method] = $callback;
			return self::find_class_file(is_object($class) ? get_class($class) : $class);
		}
		if (is_object($callback)) {
			return self::find_class_file(get_class($callback));
		}
		return null;
	}

	/**
	 * Find the file for a given class name.
	 *
	 * @param string $class_name The class name to find the file for.
	 * @return string|null The filename if found, null otherwise.
	 */
	private static function find_class_file($class_name) {
		$psr4_path = str_replace('\\', '/', $class_name) . '.php';
		$wp_path = str_replace('_', '/', $class_name) . '.php';

		foreach (self::BASE_DIRS as $base_dir) {
			$file = self::search_in_base_dir($base_dir, $psr4_path, $wp_path);
			if ($file !== null) {
				return $file;
			}
		}

		return null;
	}

	/**
	 * Search for the class file in a specific base directory.
	 *
	 * @param string $base_dir The base directory to search in.
	 * @param string $psr4_path The PSR-4 style path.
	 * @param string $wp_path The WordPress style path.
	 * @return string|null The filename if found, null otherwise.
	 */
	private static function search_in_base_dir($base_dir, $psr4_path, $wp_path) {
		foreach (self::POSSIBLE_SUBFOLDERS as $subfolder) {
			$file = self::search_in_subfolder($base_dir, $subfolder, $psr4_path, $wp_path);
			if ($file !== null) {
				return $file;
			}
		}

		return self::search_in_vendor($base_dir, $psr4_path);
	}

	/**
	 * Search for the class file in a specific subfolder.
	 *
	 * @param string $base_dir The base directory.
	 * @param string $subfolder The subfolder to search in.
	 * @param string $psr4_path The PSR-4 style path.
	 * @param string $wp_path The WordPress style path.
	 * @return string|null The filename if found, null otherwise.
	 */
	private static function search_in_subfolder($base_dir, $subfolder, $psr4_path, $wp_path) {
		$possible_paths = [
			$base_dir . '/' . $subfolder . $psr4_path,
			$base_dir . '/' . $subfolder . $wp_path,
			$base_dir . '/' . $subfolder . dirname($wp_path) . '/class-' . basename($wp_path),
			$base_dir . '/' . $subfolder . strtolower($psr4_path),
			$base_dir . '/' . $subfolder . basename($psr4_path),
		];

		foreach ($possible_paths as $path) {
			if (file_exists($path)) {
				return $path;
			}
		}

		return null;
	}

	/**
	 * Search for the class file in the vendor directory.
	 *
	 * @param string $base_dir The base directory.
	 * @param string $psr4_path The PSR-4 style path.
	 * @return string|null The filename if found, null otherwise.
	 */
	private static function search_in_vendor($base_dir, $psr4_path) {
		$vendor_path = $base_dir . '/vendor/';
		if (is_dir($vendor_path)) {
			$vendor_psr4_path = $vendor_path . $psr4_path;
			if (file_exists($vendor_psr4_path)) {
				return $vendor_psr4_path;
			}
		}
		return null;
	}
}
