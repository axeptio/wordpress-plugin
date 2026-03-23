<?php

namespace Axeptio\Plugin\Utils;

use function Axeptio\Plugin\wp_memory_limit_in_bytes;

class Search_Callback_File_Location {
	/**
	 * Cache expiration time in seconds (1 week)
	 */
	const CACHE_EXPIRATION = 604800;

	/**
	 * Maximum number of entries in the callback file locations.
	 */
	protected static int $max_callbacks = 5000;

	/**
	 * Temporary storage for callback file locations.
	 *
	 * @var array
	 */
	private static ?array $callback_file_locations = null;

	/**
	 * Initial state of the callback file locations for comparison.
	 *
	 * @var array
	 */
	private static ?array $initial_callback_file_locations = null;


	/**
	 * Get the plugin where a callback function is defined.
	 *
	 * @param mixed       $callback_function The callback function to analyze.
	 * @param string|null $name The name of the callback (optional).
	 * @param string|null $filter The filter name (optional).
	 * @param int|null    $priority The priority of the hook (optional).
	 * @return string|null The filename or null if not found.
	 */
	public static function get_plugin( $callback_function, ?string $name = null, ?string $filter = null, ?int $priority = null ): ?string {
		$filename = self::find_filename( $callback_function, $name, $filter, $priority );
		return self::extract_plugin_name( $filename );
	}

	/**
	 * Extract the plugin name from a given filename.
	 *
	 * @param string $filename The full path to the file.
	 * @return string|null The plugin name or null if not found.
	 */
	private static function extract_plugin_name( $filename ) {
		$plugin_dir = wp_normalize_path( WP_PLUGIN_DIR );
		$filename   = wp_normalize_path( $filename );

		if ( strpos( $filename, $plugin_dir ) === 0 ) {
			$relative_path = substr( $filename, strlen( $plugin_dir ) + 1 );
			$parts         = explode( '/', $relative_path );
			return $parts[0] ?? null;
		}

		return null;
	}

	/**
	 * Find the filename where a callback function is defined.
	 *
	 * @param mixed       $callback_function The callback function to analyze.
	 * @param string|null $name The name of the callback (optional, reserved for future use).
	 * @param string|null $filter The filter name (optional, reserved for future use).
	 * @param int|null    $priority The priority of the hook (optional, reserved for future use).
	 * @return string|null The filename or null if not found.
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Parameters reserved for future extensibility.
	private static function find_filename( $callback_function, ?string $name = null, ?string $filter = null, ?int $priority = null ): ?string {
		try {
			if ( is_string( $callback_function ) ) {
				return self::get_filename_from_string( $callback_function );
			}

			if ( is_array( $callback_function ) && count( $callback_function ) === 2 ) {
				return self::get_filename_from_array( $callback_function );
			}

			if ( $callback_function instanceof \Closure ) {
				$reflection = new \ReflectionFunction( $callback_function );
				return $reflection->getFileName();
			}

			if ( is_object( $callback_function ) && method_exists( $callback_function, '__invoke' ) ) {
				$reflection = new \ReflectionMethod( $callback_function, '__invoke' );
				return $reflection->getFileName();
			}
		} catch ( \ReflectionException $e ) {
			// Log the exception or handle it as needed.
			return null;
		}

		return null;
	}

	/**
	 * Get the filename from a string callback.
	 *
	 * @param string $callback_function
	 * @return string|null
	 */
	private static function get_filename_from_string( string $callback_function ): ?string {
		if ( function_exists( $callback_function ) ) {
			$reflection = new \ReflectionFunction( $callback_function );
			return $reflection->getFileName();
		} elseif ( strpos( $callback_function, '::' ) !== false ) {
			list( $class, $method ) = explode( '::', $callback_function );
			$reflection             = new \ReflectionMethod( $class, $method );
			return $reflection->getFileName();
		}
		return null;
	}

	/**
	 * Get the filename from an array callback.
	 *
	 * @param array $callback_function
	 * @return string|null
	 * @throws \ReflectionException
	 */
	private static function get_filename_from_array( array $callback_function ): ?string {
		list( $object_or_class, $method ) = $callback_function;

		if ( is_object( $object_or_class ) ) {
			$reflection = new \ReflectionMethod( get_class( $object_or_class ), $method );
		} elseif ( is_string( $object_or_class ) ) {
			$reflection = new \ReflectionMethod( $object_or_class, $method );
		} else {
			return null;
		}

		return $reflection->getFileName();
	}
}
