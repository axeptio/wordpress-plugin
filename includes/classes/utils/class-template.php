<?php
/**
 * Template loader.
 *
 * Originally based on functions in Easy Digital Downloads (thanks Pippin!).
 * When using in a plugin, create a new class that extends this one and just overrides the properties.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Utils;

class Template {
	/**
	 * Name of the directory containing templates within this plugin.
	 *
	 * It can be a predefined constant or a relative path based on the location of the subclass.
	 *
	 * Examples: 'templates' or 'includes/templates', etc.
	 *
	 * @var string
	 */
	protected string $plugin_tpl_directory = 'templates';

	/**
	 * Holds the paths of located templates.
	 *
	 * @var array<string>
	 */
	private array $tpl_path_cache = array();

	/**
	 * Stores variable names for template data.
	 *
	 * This allows unset_template_data() to clear all custom references from $wp_query.
	 *
	 * Initialized with the default value 'data'.
	 *
	 * @var array<string>
	 */
	private array $tpl_data_var_names = array( 'data' );

	/**
	 * Clear template data.
	 *
	 * @since 1.2.0
	 */
	public function __destruct() {
		$this->unset_template_data();
	}

	/**
	 * Get a template part.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $slug Template slug.
	 * @param string|null $name Optional. Template variation name. Default null.
	 * @param bool        $load Optional. Whether to load template. Default true.
	 * @return string
	 */
	public function get_template_part( string $slug, ?string $name = null, bool $load = true ): string {
		do_action( "axeptio/get_template_part_$slug", $slug, $name );

		// Get files names of templates, for given slug and name.
		$tpl_filenames = $this->get_template_file_names( $slug, $name );

		// Return the part that is found.
		return $this->locate_template( $tpl_filenames, $load, false );
	}

	/**
	 * Provide custom data to the template.
	 *
	 * Data can be accessed in the template as properties under the `$data` variable.
	 * For instance, a value provided as `$data['foo']` can be accessed as `$data->foo`.
	 *
	 * For an input key with a hyphen, use `$data->{foo-bar}` in the template.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed  $data Custom data for the template.
	 * @param string $var_name Optional. Variable under which custom data is accessible in the template.
	 * Default is 'data'.
	 * @return Template
	 */
	public function set_template_data( $data, string $var_name = 'data' ): self {
		global $wp_query;

		$wp_query->query_vars[ $var_name ] = (object) $data;

		// Add $var_name to custom variable store if not default value.
		if ( 'data' !== $var_name ) {
			$this->tpl_data_var_names[] = $var_name;
		}

		return $this;
	}

	/**
	 * Deny access to custom data in the template.
	 *
	 * Useful after the final template part has been requested.
	 *
	 * @since 1.2.0
	 *
	 * @return Template
	 */
	public function unset_template_data(): self {
		global $wp_query;

		// Remove any duplicates from the custom variable store.
		$custom_var_names = array_unique( $this->tpl_data_var_names );

		// Remove each custom data reference from $wp_query.
		foreach ( $custom_var_names as $var ) {
			if ( isset( $wp_query->query_vars[ $var ] ) ) {
				unset( $wp_query->query_vars[ $var ] );
			}
		}

		return $this;
	}

	/**
	 * Generate template file names based on a slug and optional name.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $slug Template slug.
	 * @param string|null $name Template variation name.
	 * @return array<string>
	 */
	protected function get_template_file_names( string $slug, ?string $name ): array {
		$tpls = array();
		if ( isset( $name ) ) {
			$tpls[] = $slug . '-' . $name . '.php';
		}
		$tpls[] = $slug . '.php';

		/**
		 * Filter template options.
		 *
		 * The resulting array should have the most specific template first, and the least specific last.
		 * e.g. 0 => recipe-instructions.php, 1 => recipe.php
		 *
		 * @since 1.0.0
		 *
		 * @param array<string> $tpls Template file names to search for, given slug and name.
		 * @param string $slug Template slug.
		 * @param string|null $name Template variation name.
		 */
		return apply_filters( 'axeptio/get_template_part', $tpls, $slug, $name );
	}

	/**
	 * Find the highest priority template file that exists.
	 *
	 * Searches STYLESHEETPATH before TEMPLATEPATH to allow child themes to override a single file. If not found in
	 * either location, checks the theme-compat folder last.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array<string> $tpl_names Template files to search for, in order.
	 * @param bool                 $load If true, the template file will be loaded if found.
	 * @param bool                 $load_once Whether to require_once or require. Default true.
	 *                 No effect if $load is false.
	 * @return string The template filename if located.
	 */
	public function locate_template( $tpl_names, bool $load = false, bool $load_once = true ): string {
		// Use $tpl_names as a cache key - either first element of array or the variable itself if it's a string.
		$cache_key = is_array( $tpl_names ) ? $tpl_names[0] : $tpl_names;

		// If the key is in the cache array, we've already located this file.
		if ( isset( $this->tpl_path_cache[ $cache_key ] ) ) {
			$located = $this->tpl_path_cache[ $cache_key ];

		} else {
			// Remove empty entries.
			$tpl_names = array_filter( (array) $tpl_names );
			$tpl_paths = $this->get_template_paths();

			// Generate an array of possible file paths.
			$possible_paths = array_reduce(
				$tpl_names,
				function ( $carry, $tpl_name ) use ( $tpl_paths ) {
					$tpl_name  = ltrim( $tpl_name, '/' );
					$new_paths = array_map( fn( $tpl_path) => $tpl_path . $tpl_name, $tpl_paths );
					return array_merge( $carry, $new_paths );
				},
				array()
				);

			// Locate the first existing file in the possible paths.
			$located = array_reduce(
				$possible_paths,
				function ( $found, $path ) {
					return $found ? $found : ( file_exists( $path ) ? $path : false );
				},
				false
				);

			// Store the template path in the cache.
			if ( $located ) {
				$this->tpl_path_cache[ $cache_key ] = $located;
			}
		}

		if ( $load && $located ) {
			load_template( $located, $load_once );
		}

		return $located;
	}

	/**
	 * Generate a list of paths for template locations.
	 *
	 * By default, checks the child theme (if applicable) before the parent theme, allowing child themes to override a
	 * single file. If not found in either, checks the theme-compat folder last.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string>
	 */
	protected function get_template_paths(): array {
		/**
		 * Permit modification of the ordered list of template paths.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string> $var Default template paths.
		 */
		$file_paths = apply_filters(
			'axeptio/template_paths',
			array(
				$this->get_templates_dir(),
			)
			);

		// Sort the file paths based on priority.
		ksort( $file_paths, SORT_NUMERIC );

		return array_map( 'trailingslashit', $file_paths );
	}

	/**
	 * Retrieve the path to the templates directory within this plugin.
	 *
	 * Can be overridden in a subclass.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_templates_dir(): string {
		return trailingslashit( XPWP_PATH ) . $this->plugin_tpl_directory;
	}
}
