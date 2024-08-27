<?php

namespace Axeptio\Plugin\Utils;

class Search_Callback_File_Location {

	/**
	 * Cache group name
	 */
	const CACHE_GROUP = 'axeptio_callback_locations';

	/**
	 * Cache expiration time in seconds (1 hour)
	 */
	const CACHE_EXPIRATION = 3600;

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
		$cache_key     = self::generate_cache_key( $callback_function, $name, $filter, $priority );
		$cached_result = wp_cache_get( $cache_key, self::CACHE_GROUP );

		if ( false !== $cached_result && ! is_null( $cached_result ) ) {
			return $cached_result;
		}

		$filename = self::find_filename( $callback_function, $name, $filter, $priority );

		wp_cache_set( $cache_key, $filename, self::CACHE_GROUP, self::CACHE_EXPIRATION );

		return $filename;
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
			// Handle string callbacks (function names)
			if ( is_string( $callback_function ) ) {
				if ( function_exists( $callback_function ) ) {
					$reflection = new \ReflectionFunction( $callback_function );
					return $reflection->getFileName();
				} elseif ( strpos( $callback_function, '::' ) !== false ) {
					// Static method call string like 'Class::method'
					list($class, $method) = explode( '::', $callback_function );
					$reflection           = new \ReflectionMethod( $class, $method );
					return $reflection->getFileName();
				}
			}

			// Handle array callbacks
			if ( is_array( $callback_function ) && count( $callback_function ) === 2 ) {
				list($object_or_class, $method) = $callback_function;

				if ( is_object( $object_or_class ) ) {
					$reflection = new \ReflectionMethod( get_class( $object_or_class ), $method );
				} elseif ( is_string( $object_or_class ) ) {
					$reflection = new \ReflectionMethod( $object_or_class, $method );
				} else {
					return null;
				}

				return $reflection->getFileName();
			}

			// Handle Closure
			if ( $callback_function instanceof \Closure ) {
				$reflection = new \ReflectionFunction( $callback_function );
				return $reflection->getFileName();
			}

			// Handle invokable objects
			if ( is_object( $callback_function ) && method_exists( $callback_function, '__invoke' ) ) {
				$reflection = new \ReflectionMethod( $callback_function, '__invoke' );
				return $reflection->getFileName();
			}

			// Handle WordPress-specific cases
			if ( is_string( $name ) && is_string( $filter ) ) {
				return self::handle_wp_specific_cases( $name, $filter, $priority );
			}
		} catch ( \ReflectionException $e ) {
			// Log the exception or handle it as needed
			return null;
		}

		// If we couldn't determine the file name, return null
		return null;
	}

	/**
	 * Generate a unique cache key for the given parameters.
	 *
	 * @param mixed       $callback_function
	 * @param string|null $name
	 * @param string|null $filter
	 * @param int|null    $priority
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
			if ( $priority !== null && isset( $hooks->callbacks[ $priority ][ $name ] ) ) {
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
}
