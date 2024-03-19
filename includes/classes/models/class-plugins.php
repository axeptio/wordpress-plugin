<?php
/**
 * Plugins Model
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Models;

use Axeptio\Plugin\Utils\Remember;
use function Axeptio\Plugin\Utility\get_favicon;

class Plugins {
	/**
	 * Plugin object.
	 *
	 * @var mixed $plugin
	 */
	public $plugin;

	/**
	 * Axeptio Configuration ID.
	 *
	 * @var string $configuration_id
	 */
	public $configuration_id;

	/**
	 * Plugin ID.
	 *
	 * @var string $plugin_id
	 */
	public $plugin_id;

	/**
	 * Every metas.
	 *
	 * @var array|null $all_metas
	 */
	public static $all_metas = null;

	/**
	 * Axeptio plugin configurable table name.
	 *
	 * @var string $table_name
	 */
	public static $table_name = 'axeptio_plugin_configuration';

	/**
	 * Register the table in the $wpdb object.
	 *
	 * @return void
	 */
	public static function register_plugin_configuration_table() {
		global $wpdb;

		if ( in_array( self::$table_name, $wpdb->tables, true ) ) {
			return;
		}

		$wpdb->self::$table_name = $wpdb->prefix . self::$table_name;
		$wpdb->tables[]          = self::$table_name;
	}

	/**
	 * Get all plugins.
	 *
	 * @param string $configuration_id Configuration ID.
	 * @param bool   $force_refresh      Force use a fresh value (not cache).
	 * @return array Array of plugins.
	 */
	public static function all( string $configuration_id = 'all', $force_refresh = false ): array {
		return Remember::get_or_reset_result(
			'plugins_all',
			function () use ( $configuration_id, $force_refresh ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
				$plugins = \get_plugins();

				$plugin_list = array();

				foreach ( $plugins as $key => $plugin ) {
					$plugin_key = self::get_plugin_id( $key );

					if ( 'axeptio-wordpress-plugin' === $plugin_key ) {
						continue;
					}

					$plugin_metadatas = self::get_meta_datas( $plugin_key, $configuration_id, $force_refresh );

					if ( ! isset( $plugin_metadatas['Merged']['vendor_image'] ) ) {
						$plugin_metadatas['Merged']['vendor_image'] = get_favicon( $plugin['PluginURI'] );
					}

					$plugin_list[ $plugin_key ] = array_merge(
					$plugin,
					array(
						'AxeptioRecommendedSettings' => Recommended_Plugin_Settings::find( $plugin_key ),
						'Metas'                      => $plugin_metadatas,
						'HookModes'                  => Hook_Modes::all( $configuration_id, $plugin_key ),
						'ShortcodeTagsModes'         => Shortcode_Tags_Modes::all( $configuration_id, $plugin_key ),
					)
					);
				}

				return $plugin_list;
			},
			$configuration_id,
			$force_refresh
			);
	}

	/**
	 * Get the plugin.
	 *
	 * @return mixed Plugin object.
	 */
	public function get() {
		return $this->plugin;
	}

	/**
	 * Constructor.
	 *
	 * @param string $plugin_id Plugin ID.
	 * @param string $configuration_id Configuration ID.
	 */
	public function __construct( string $plugin_id, string $configuration_id = 'all' ) {
		$this->plugin_id        = $plugin_id;
		$this->configuration_id = $configuration_id;

		$plugins      = self::all( $this->configuration_id );
		$this->plugin = isset( $plugins[ $this->plugin_id ] ) ? $plugins[ $this->plugin_id ] : null;
	}

	/**
	 * Find every plugin configurations.
	 *
	 * @return array
	 */
	public static function find_all(): array {
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM `$wpdb->axeptio_plugin_configuration` ORDER BY axeptio_configuration_id = 'all' DESC", ARRAY_A ); // @codingStandardsIgnoreLine
	}

	/**
	 * Find a plugin by its slug.
	 *
	 * @param string $plugin Plugin slug to find.
	 * @param string $configuration_id The Axeptio configuration ID.
	 * @return self
	 */
	public static function find( string $plugin, string $configuration_id = 'all' ): self {
		return new static( $plugin, $configuration_id );
	}

	/**
	 * Update the plugin metadata.
	 *
	 * @param array $meta_datas New metadata.
	 * @return int|false
	 */
	public function update( array $meta_datas ) {
		global $wpdb;

		// If it does not exist, create it.
		// ...
		if ( null === $this->plugin || false === $this->plugin['Metas']['enabled'] ) {
			$this->create( $meta_datas );
		} else {
			$where = array(
				'axeptio_configuration_id' => $this->configuration_id,
				'plugin'                   => $this->plugin_id,
			);
			$wpdb->update( $wpdb->axeptio_plugin_configuration, $meta_datas, $where ); // @codingStandardsIgnoreLine
		}

		$plugins      = self::all( $this->configuration_id, true );
		$this->plugin = isset( $plugins[ $this->plugin_id ] ) ? $plugins[ $this->plugin_id ] : null;

		return $this->plugin;
	}

	/**
	 * Delete the plugin metadata.
	 *
	 * @return int|false
	 */
	public function delete(): bool {
		global $wpdb;

		$where = array(
			'axeptio_configuration_id' => $this->configuration_id,
			'plugin'                   => $this->plugin_id,
		);

		return $wpdb->delete( // @codingStandardsIgnoreLine
			$wpdb->prefix . self::$table_name,
			$where
		);
	}

	/**
	 * Create a new plugin entry in the database.
	 *
	 * @param array $meta_datas Plugin metadata.
	 * @return int|false
	 */
	public function create( array $meta_datas ) {
		global $wpdb;

		$defaults   = $this->get_default_metadatas();
		$meta_datas = wp_parse_args( $meta_datas, $defaults );
		return $wpdb->insert( $wpdb->axeptio_plugin_configuration, $meta_datas ); // @codingStandardsIgnoreLine
	}

	/**
	 * Get the default metadata values for a plugin.
	 *
	 * @return array Default metadata values.
	 */
	public function get_default_metadatas(): array {
		return array(
			'wp_filter_mode'                      => 'none',
			'wp_filter_list'                      => '',
			'wp_filter_store_output'              => 0,
			'wp_filter_reload_page_after_consent' => 'no',
			'shortcode_tags_mode'                 => 'none',
			'shortcode_tags_list'                 => '',
			'shortcode_tags_placeholder'          => '',
			'vendor_id'                           => 0,
			'vendor_title'                        => '',
			'vendor_shortDescription'             => '',
			'vendor_longDescription'              => '',
			'vendor_policyUrl'                    => '',
			'vendor_image'                        => '',
			'cookie_widget_step'                  => 0,
		);
	}

	/**
	 * Get the plugin ID from the given key.
	 *
	 * @param string $key Plugin key.
	 * @return string Plugin ID.
	 */
	protected static function get_plugin_id( $key ): string {
		if ( str_contains( $key, '/' ) ) {
			$key = explode( '/', $key )[0];
		}
		return sanitize_title( str_replace( '.php', '', $key ) );
	}

	/**
	 * Fix value formats.
	 *
	 * @param array $metas Metas datas.
	 * @return array
	 */
	protected static function fix_metadata_format( array $metas ): array {
		if ( isset( $metas['wp_filter_store_output'] ) ) {
			$metas['wp_filter_store_output'] = intval( $metas['wp_filter_store_output'] );
		}
		if ( isset( $metas['enabled'] ) ) {
			$metas['enabled'] = intval( $metas['enabled'] );
		}
		return $metas;
	}

	/**
	 * Collect every meta datas and prepare them.
	 *
	 * @param bool $force_refresh      Force use a fresh value (not cache).
	 * @return array Metas datas.
	 */
	public static function prepare_meta_data( $force_refresh = false ) {
		if ( self::$all_metas && ! $force_refresh ) {
			return self::$all_metas;
		}

		$meta_datas   = self::find_all();
		$output_metas = array();

		foreach ( $meta_datas as $meta_data ) {
			$configuration_id = $meta_data['axeptio_configuration_id'];
			$plugin           = $meta_data['plugin'];
			$meta_data        = self::fix_metadata_format( $meta_data );

			if ( ! isset( $output_metas[ $configuration_id ] ) ) {
				$output_metas[ $configuration_id ] = array();
			}

			$output_metas[ $configuration_id ][ $plugin ] = $meta_data;

			if ( 'all' !== $configuration_id && isset( $output_metas['all'][ $plugin ] ) ) {
				$merged_meta = wp_parse_args( self::remove_empty_string_values( $output_metas[ $configuration_id ][ $plugin ] ), $output_metas['all'][ $plugin ] );
				$output_metas[ $configuration_id ][ $plugin ]['Merged'] = $merged_meta;
				$output_metas[ $configuration_id ][ $plugin ]['Parent'] = $output_metas['all'][ $plugin ];
				$output_metas[ $configuration_id ][ $plugin ]           = self::remove_empty_string_values( $output_metas[ $configuration_id ][ $plugin ] );
			}
		}

		self::$all_metas = $output_metas;
		return self::$all_metas;
	}

	/**
	 * Remove empty string values from specified keys in an array.
	 *
	 * @param array $meta_array Array to remove empty values from.
	 * @return array              Modified array.
	 */
	protected static function remove_empty_string_values( array $meta_array ): array {
		foreach ( array( 'vendor_title', 'vendor_shortDescription', 'vendor_longDescription', 'vendor_policyUrl', 'vendor_image' ) as $key ) {
			if ( isset( $meta_array[ $key ] ) && '' === $meta_array[ $key ] ) {
				unset( $meta_array[ $key ] );
			}
		}

		return $meta_array;
	}


	/**
	 * Get the metadata of a plugin.
	 *
	 * @param string $plugin_key Plugin key.
	 * @param string $configuration_id Configuration ID.
	 * @param bool   $force_refresh      Force use a fresh value (not cache).
	 * @return mixed Plugin metadata.
	 */
	public static function get_meta_datas( $plugin_key, $configuration_id = 'all', $force_refresh = false ) {
		$all_metas = self::prepare_meta_data( $force_refresh );

		if ( isset( $all_metas[ $configuration_id ][ $plugin_key ] ) ) {
			return $all_metas[ $configuration_id ][ $plugin_key ];
		}

		if ( isset( $all_metas['all'][ $plugin_key ] ) ) {

			$metas = array(
				'plugin'                   => self::get_plugin_id( $plugin_key ),
				'axeptio_configuration_id' => $configuration_id,
				'enabled'                  => false,
				'wp_filter_mode'           => 'all' === $configuration_id ? 'none' : 'inherit',
				'wp_filter_list'           => '',
				'shortcode_tags_mode'      => 'all' === $configuration_id ? 'none' : 'inherit',
				'shortcode_tags_list'      => '',
			);

			$merged_meta                = wp_parse_args( $metas, $all_metas['all'][ $plugin_key ] );
			$metas['Merged']            = $merged_meta;
			$metas['Merged']['enabled'] = false === $metas['Merged']['enabled'] ? $all_metas['all'][ $plugin_key ]['enabled'] : $metas['Merged']['enabled'];
			$metas['Parent']            = $all_metas['all'][ $plugin_key ];

			return self::remove_empty_string_values( $metas );
		}

		$recommended_settings = Recommended_Plugin_Settings::find( $plugin_key );

		return array(
			'plugin'                   => self::get_plugin_id( $plugin_key ),
			'axeptio_configuration_id' => $configuration_id,
			'enabled'                  => false,
			'wp_filter_mode'           => 'all' === $configuration_id ? ( $recommended_settings && isset( $recommended_settings['wp_filter_mode'] ) ? 'recommended' : 'none' ) : 'inherit',
			'wp_filter_list'           => '',
			'shortcode_tags_mode'      => 'all' === $configuration_id ? ( $recommended_settings && isset( $recommended_settings['shortcode_tags_mode'] ) ? 'recommended' : 'none' ) : 'inherit',
			'shortcode_tags_list'      => '',
		);
	}

	/**
	 * Get the list of active plugins.
	 *
	 * @return array List of active plugins.
	 */
	public static function get_active_plugins(): array {
		$active_plugins = get_option( 'active_plugins' );

		$plugins = array();
		foreach ( $active_plugins as $key => $value ) {
			$plugins[] = self::get_plugin_id( $value );
		}

		return $plugins;
	}
}
