<?php

namespace Axeptio\Plugin\Utils;

class Search_Callback_File_Location {

	/**
	 * Cache group name
	 */
	const CACHE_GROUP = 'axeptio_callback_locations';

	/**
	 * Cache expiration time in seconds (1 week)
	 */
	const CACHE_EXPIRATION = 604800;

	/**
	 * Temporary storage for callback file locations.
	 *
	 * @var array
	 */
	private static $callback_file_locations = null;

	/**
	 * Initial state of the callback file locations for comparison.
	 *
	 * @var array
	 */
	private static $initial_callback_file_locations = null;

	/**
	 * Initialize the callback file locations from cache.
	 *
	 * @return void
	 */
	public static function initialize_cache() {
		if (self::$callback_file_locations !== null) {
			return;
		}

		$cache_file = self::get_cache_file_path();

		if (file_exists($cache_file) && (time() - filemtime($cache_file)) < self::CACHE_EXPIRATION) {
			self::$callback_file_locations = (array) include $cache_file;
		} else {
			self::$callback_file_locations = array();
		}

		// Store the initial state for later comparison
		self::$initial_callback_file_locations = self::$callback_file_locations;
	}

	/**
	 * Get the filename where a callback function is defined.
	 *
	 * @param mixed       $callback_function The callback function to analyze.
	 * @param string|null $name The name of the callback (optional).
	 * @param string|null $filter The filter name (optional).
	 * @param int|null    $priority The priority of the hook (optional).
	 * @return string|null The filename or null if not found.
	 */
	public static function get_filename( $callback_function, ?string $name = null, ?string $filter = null, ?int $priority = null ): ?string {
		self::initialize_cache();

		$cache_key = self::generate_cache_key( $callback_function, $name, $filter, $priority );

		// Check if the filename is already in the temporary storage.
		if ( isset( self::$callback_file_locations[ $cache_key ] ) ) {
			return self::$callback_file_locations[ $cache_key ];
		}

		$filename = self::find_filename( $callback_function, $name, $filter, $priority );

		// Store the result in the temporary storage.
		self::$callback_file_locations[ $cache_key ] = $filename;

		return $filename;
	}

	/**
	 * Write the callback file locations to a PHP file.
	 *
	 * @return void
	 */
	public static function write_cache_to_file() {
		// Sort both arrays to ensure they are in the same order for comparison
		ksort(self::$callback_file_locations);
		ksort(self::$initial_callback_file_locations);

		// Only write to the file if the data has changed
		if (self::$callback_file_locations === self::$initial_callback_file_locations) {
			return;
		}

		$cache_file = self::get_cache_file_path();

		// Check if the directory exists, if not, create it.
		self::ensure_cache_directory_exists();

		$content = "<?php\nreturn " . var_export( self::$callback_file_locations, true ) . ";\n";

		file_put_contents( $cache_file, $content );
	}

	/**
	 * Find the filename where a callback function is defined.
	 *
	 * @param mixed       $callback_function The callback function to analyze.
	 * @param string|null $name The name of the callback (optional).
	 * @param string|null $filter The filter name (optional).
	 * @param int|null    $priority The priority of the hook (optional).
	 * @return string|null The filename or null if not found.
	 */
	private static function find_filename( $callback_function, ?string $name = null, ?string $filter = null, ?int $priority = null ): ?string {
		try {
			if (is_string($callback_function)) {
				return self::get_filename_from_string($callback_function);
			}

			if (is_array($callback_function) && count($callback_function) === 2) {
				return self::get_filename_from_array($callback_function);
			}

			if ($callback_function instanceof \Closure) {
				$reflection = new \ReflectionFunction($callback_function);
				return $reflection->getFileName();
			}

			if (is_object($callback_function) && method_exists($callback_function, '__invoke')) {
				$reflection = new \ReflectionMethod($callback_function, '__invoke');
				return $reflection->getFileName();
			}

			if (is_string($name) && is_string($filter)) {
				return self::handle_wp_specific_cases($name, $filter, $priority);
			}
		} catch (\ReflectionException $e) {
			// Log the exception or handle it as needed.
			return null;
		}

		return null;
	}

	/**
	 * Generate a unique cache key for the given parameters.
	 *
	 * @param mixed       $callback_function The callback function.
	 * @param string|null $name              The name of the callback.
	 * @param string|null $filter            The filter name.
	 * @param int|null    $priority          The priority of the hook.
	 * @return string
	 */
	private static function generate_cache_key( $callback_function, ?string $name, ?string $filter, ?int $priority ): string {
		$key_parts = array(
			self::get_callback_identifier( $callback_function ),
			$name,
			$filter,
			$priority,
		);

		return md5( implode( '|', array_filter( $key_parts ) ) );
	}

	/**
	 * Get a unique identifier for the callback function.
	 *
	 * @param mixed $callback_function The callback function.
	 * @return string A unique identifier for the callback.
	 */
	private static function get_callback_identifier( $callback_function ): string {
		if ( is_string( $callback_function ) ) {
			return $callback_function;
		}
		if ( is_array( $callback_function ) && count( $callback_function ) === 2 ) {
			if ( is_object( $callback_function[0] ) ) {
				return get_class( $callback_function[0] ) . '::' . $callback_function[1];
			}
			return $callback_function[0] . '::' . $callback_function[1];
		}
		if ( $callback_function instanceof \Closure ) {
			$reflection = new \ReflectionFunction( $callback_function );
			return 'closure:' . $reflection->getFileName() . ':' . $reflection->getStartLine();
		}
		if ( is_object( $callback_function ) ) {
			return get_class( $callback_function ) . '::__invoke';
		}
		return 'unknown';
	}

	/**
	 * Handle WordPress-specific callback cases.
	 *
	 * @param string   $name The name of the callback.
	 * @param string   $filter The filter name.
	 * @param int|null $priority The priority of the hook.
	 * @return string|null The filename or null if not found.
	 */
	private static function handle_wp_specific_cases( string $name, string $filter, ?int $priority ): ?string {
		global $wp_filter;

		if ( isset( $wp_filter[ $filter ] ) ) {
			$hooks = $wp_filter[ $filter ];
			if ( null !== $priority && isset( $hooks->callbacks[ $priority ][ $name ] ) ) {
				$callback = $hooks->callbacks[ $priority ][ $name ]['function'];
				return self::get_filename( $callback );
			} else {
				foreach ( $hooks->callbacks as $prio => $callbacks ) {
					if ( isset( $callbacks[ $name ] ) ) {
						$callback = $callbacks[ $name ]['function'];
						return self::get_filename( $callback );
					}
				}
			}
		}

		return null;
	}

	/**
	 * Get the cache file path.
	 *
	 * @return string
	 */
	private static function get_cache_file_path(): string {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'] . '/axeptio/callback_file_cache.php';
	}

	/**
	 * Ensure the cache directory exists.
	 *
	 * @return void
	 */
	private static function ensure_cache_directory_exists() {
		$cache_dir = dirname(self::get_cache_file_path());
		if (!file_exists($cache_dir)) {
			wp_mkdir_p($cache_dir);
		}
	}

	/**
	 * Get the filename from a string callback.
	 *
	 * @param string $callback_function
	 * @return string|null
	 */
	private static function get_filename_from_string(string $callback_function): ?string {
		if (function_exists($callback_function)) {
			$reflection = new \ReflectionFunction($callback_function);
			return $reflection->getFileName();
		} elseif (strpos($callback_function, '::') !== false) {
			list($class, $method) = explode('::', $callback_function);
			$reflection = new \ReflectionMethod($class, $method);
			return $reflection->getFileName();
		}
		return null;
	}

	/**
	 * Get the filename from an array callback.
	 *
	 * @param array $callback_function
	 * @return string|null
	 */
	private static function get_filename_from_array(array $callback_function): ?string {
		list($object_or_class, $method) = $callback_function;

		if (is_object($object_or_class)) {
			$reflection = new \ReflectionMethod(get_class($object_or_class), $method);
		} elseif (is_string($object_or_class)) {
			$reflection = new \ReflectionMethod($object_or_class, $method);
		} else {
			return null;
		}

		return $reflection->getFileName();
	}
}
