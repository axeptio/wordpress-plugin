<?php
namespace Axeptio\Utils;

class Remember {
	/**
	 * Static cache array to store results.
	 *
	 * @var array $cache
	 */
	private static array $cache = array();

	/**
	 * Retrieves the result from cache or executes the callback to generate and cache the result.
	 *
	 * @param string   $identifier An identifier for the cached result, typically the function name or a unique identifier.
	 * @param callable $callback The callback function to execute if the result is not found in the cache.
	 * @param mixed    ...$args Variable list of arguments to pass to the callback function.
	 * @return mixed The result from the cache or the newly generated result from the callback.
	 */
	public static function get_or_reset_result( string $identifier, callable $callback, ...$args ) {

		// Generating a cache key based on the identifier and arguments.
		$cache_key = self::generate_cache_key( $identifier, $args );

		// Checking if the result is already cached.
		if ( isset( self::$cache[ $cache_key ] ) ) {
			return self::$cache[ $cache_key ];
		}

		// If the result is not in cache, call the callback function with arguments and store the result.
		$result = call_user_func_array( $callback, $args );

		// Storing the result in cache before returning it.
		self::$cache[ $cache_key ] = $result;

		return $result;
	}

	/**
	 * Generates a unique cache key based on the callback identifier and its arguments.
	 *
	 * @param mixed $callback The callback function or identifier.
	 * @param array $args The arguments passed to the callback function.
	 * @return string A unique MD5 hash as the cache key.
	 */
	private static function generate_cache_key( $callback, $args ) {
		// Serializing the function/callback name and arguments to create a unique key.
		$callback_name = is_array( $callback ) ? implode( '::', $callback ) : $callback;

		return md5( $callback_name . maybe_serialize( $args ) );
	}

	/**
	 * Clears all stored results from the cache.
	 */
	public static function clear_cache() {
		self::$cache = array();
	}
}
