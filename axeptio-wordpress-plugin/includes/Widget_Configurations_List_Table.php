<?php

namespace Axeptio;

class Widget_Configurations_List_Table extends \WP_List_Table {

	private $_axeptio_configurations_map = [];

	public function __construct() {
		parent::__construct( [
			'singular' => 'widget_configuration',
			'plural'   => 'widget_configurations',
			'ajax'     => false
		] );

		if ( isset( Admin::instance()->axeptioConfiguration->cookies ) ) {
			foreach ( Admin::instance()->axeptioConfiguration->cookies as $cookie_configuration ) {
				$this->_axeptio_configurations_map[ $cookie_configuration->identifier ] = $cookie_configuration;
			}
		}
	}


	public function prepare_items() {
		global $wpdb;
		$this->process_action();
		$this->process_bulk_action();
		$columns  = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden   = [];

		$this->_column_headers = [
			$columns,
			$hidden,
			$sortable
		];
		$table                 = Admin::getWidgetConfigurationsTable();
		$this->items           = $wpdb->get_results( "SELECT * FROM `$table`", ARRAY_A );
	}


	private function process_action() {

	}

	private function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			foreach ( $_POST['widget_configuration'] as $item ) {
				//todo
			}
		}

	}


	public function get_columns() {
		return [
			'cb'                       => '<input type="checkbox" />',
			'axeptio_configuration_id' => "Axeptio Config",
			'step_title'               => "Title",
			'step_image'               => "Image",
			'step_message'             => "Message",
			'actions'                  => 'Actions',
		];
	}

	function get_bulk_actions() {
		return [
			'delete' => 'Delete'
		];
	}


	protected function get_sortable_columns() {
		return [];
	}

	protected function column_default( $item, $column_name ) {
		return (string) $item[ $column_name ];
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item['axeptio_configuration_id']
		);
	}

	/**
	 * @noinspection PhpUnused
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_axeptio_configuration_id( $item ) {
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

	public function column_actions( $item ) {
		$url = Admin::getWidgetConfigurationURI( $item );

		return "<div class='row-actions visible'>
					<a href='$url'>Edit</a>
				</div>";
	}

}
