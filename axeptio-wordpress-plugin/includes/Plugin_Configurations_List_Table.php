<?php

namespace Axeptio;

class Plugin_Configurations_List_Table extends \WP_List_Table {

	private array $_plugins_map = [];
	private array $_axeptio_configurations_map = [];

	public function __construct() {
		parent::__construct( [
			'singular' => 'plugin_configuration',
			'plural'   => 'plugin_configurations',
			'ajax'     => false
		] );
		foreach ( get_plugins() as $filename => $plugin ) {
			$key = strpos( $filename, "/" ) === false ? basename( $filename ) : dirname( $filename );
			$this->_plugins_map[ $key ] = $plugin;
		};

		if(isset(Admin::instance()->axeptioConfiguration->cookies)) {
			foreach ( Admin::instance()->axeptioConfiguration->cookies as $cookie_configuration ) {
				$this->_axeptio_configurations_map[ $cookie_configuration->identifier ] = $cookie_configuration;
			}
		}
	}


	public function prepare_items() {
		global $wpdb;
		$this->process_action();
		$this->process_bulk_action();
		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden = [];

		$this->_column_headers = [
			$columns,
			$hidden,
			$sortable
		];
		$table = Admin::getPluginConfigurationsTable();
		$this->items = $wpdb->get_results( "SELECT * FROM `$table`", ARRAY_A );
	}


	private function process_action() {

	}

	private function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			foreach ( $_POST['plugin_configuration'] as $item ) {
				$parts = explode( '/', $item );
				$plugin = $parts[0];
				$configuration = $parts[1];
				Admin::instance()->deletePluginConfiguration( [
					"plugin"                   => $plugin,
					"axeptio_configuration_id" => $configuration
				] );
			}
		}

	}


	public function get_columns(): array {
		return [
			'cb'                       => '<input type="checkbox" />',
			'plugin'                   => "Plugin",
			'axeptio_configuration_id' => "Axeptio Config",
			'wp_filter'                => "Intercept WP_Filter",
			'shortcode_tags'           => "Intercept Shortcodes",
			'vendor'                   => "Vendor"
		];
	}

	function get_bulk_actions(): array {
		return [
			'delete' => 'Delete'
		];
	}


	protected function get_sortable_columns(): array {
		return [
			"plugin",
			"axeptio_configuration_id",
			"wp_filter",
			"shortcode_tags"
		];
	}

	protected function column_default( $item, $column_name ): string {
		return (string) $item[ $column_name ];
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item['plugin'] . "/" . $item['axeptio_configuration_id']
		);
	}

	/**
	 * @noinspection PhpUnused
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_plugin( $item ): string {
		if ( ! isset( $this->_plugins_map[ $item["plugin"] ] ) ) {
			return "<strong>Unknown plugin $item[plugin]</strong>";
		}
		$plugin = $this->_plugins_map[ $item["plugin"] ];

		return "
                    <strong>$plugin[Title]</strong>
                    <p>$plugin[Description]</p>
                    <div class='row-actions visible'>
                        <span>
                            <a href='$plugin[PluginURI]'>Visit plugin site</a>
                        </span>
                    </div>";
	}

	/**
	 * @noinspection PhpUnused
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_vendor( $item ): string {
		$edit_url = Admin::getPluginConfigurationURI( $item );

		return "
			<strong>$item[vendor_title]</strong>
			<p>$item[vendor_shortDescription]</p>
			<div class='row-actions visible'>
				<span><a href='$item[vendor_privacy_policy_url]'>Privacy Policy</a></span> |
				<span><a href='$edit_url'>Edit Vendor Texts</a></span>
			</div>
		";
	}

	/**
	 * @noinspection PhpUnused
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_axeptio_configuration_id( $item ): string {
		if ( ! isset( $this->_axeptio_configurations_map[ $item["axeptio_configuration_id"] ] ) ) {
			return "<strong>No Configuration</strong>";
		}
		$axeptio_config = $this->_axeptio_configurations_map[ $item["axeptio_configuration_id"] ];

		return "<strong>$axeptio_config->title <code>$axeptio_config->name</code></strong>
				<p>
					<div class='row-actions visible'>
                        <span>Language: <code>$axeptio_config->language</code></span> |
                        <span>Id: <code>$axeptio_config->identifier</code></span>
                    </div>
				</p>";
	}

	/**
	 * @noinspection PhpUnused
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_wp_filter( $item ): string {
		return $item['wp_filter_mode'];
	}

	/**
	 * @noinspection PhpUnused
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_shortcode_tags( $item ): string {
		return $item['shortcode_tags_mode'];
	}

}