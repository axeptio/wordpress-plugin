<?php
/**
 * Plugins Rest API
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Admin\Rest;

use Axeptio\Plugin\Models\Notice;
use Axeptio\Plugin\Models\Plugins as PluginModel;
use Axeptio\Plugin\Module;
use WP_REST_Request;
use WP_REST_Response;

class Plugins extends Module {
	/**
	 * Checks whether the Module should run within the current context.
	 *
	 * @return bool
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register the admin menu and fields.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Restrict REST API to only admin pages.
	 *
	 * @return bool
	 */
	public function permission_callback() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Register the REST API routes.
	 *
	 * @return void
	 */
	public function register_rest_routes() {
		PluginModel::register_plugin_configuration_table();
		register_rest_route(
			'axeptio/v1',
			'/plugins/(?P<axeptio_configuration_id>([a-zA-Z0-9_ -]|%20)+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_plugins' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			),
		);

		register_rest_route(
			'axeptio/v1',
			'/plugins/(?P<axeptio_configuration_id>([a-zA-Z0-9_ -]|%20)+)/(?P<plugin>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_plugin' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			'axeptio/v1',
			'/plugins/(?P<axeptio_configuration_id>([a-zA-Z0-9_ -]|%20)+)/(?P<plugin>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_plugin' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			'axeptio/v1',
			'/disable-notice',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'disable_notice' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);

		register_rest_route(
			'axeptio/v1',
			'/timeout-notice',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'timeout_notice' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
	}

	/**
	 * Get the list of plugins.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array Array of installed plugins.
	 */
	public function get_plugins( WP_REST_Request $request ) {
		$configuration_id = urldecode( $request->get_param( 'axeptio_configuration_id' ) );
		$plugins          = PluginModel::all( $configuration_id );

		if ( $plugins ) {
			return $plugins;
		}

		return array();
	}

	/**
	 * Get the list of plugins.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Array of installed plugins.
	 */
	public function update_plugin( WP_REST_Request $request ) {
		$configuration_id = urldecode( $request->get_param( 'axeptio_configuration_id' ) );
		$plugin           = $request->get_param( 'plugin' );
		$metas            = array_diff_key( $request->get_params(), array_flip( array( 'Merged', 'Parent', 'authorized' ) ) );

		$query_datas = PluginModel::find( $plugin, $configuration_id )->update( $metas );

		return $query_datas;
	}

	/**
	 * Delete a plugin.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array Array of installed plugins.
	 */
	public function delete_plugin( WP_REST_Request $request ) {
		$configuration_id = urldecode( $request->get_param( 'axeptio_configuration_id' ) );
		$plugin           = $request->get_param( 'plugin' );
		PluginModel::find( $plugin, $configuration_id )->delete();

		return array();
	}

	/**
	 * Set the disable notice meta as true on the current user.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array
	 */
	public function disable_notice( WP_REST_Request $request ) {
		Notice::disable();

		return array();
	}

	/**
	 * Set the timeout notice cookie.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return array
	 */
	public function timeout_notice( WP_REST_Request $request ) {
		Notice::set_timeout();

		return array();
	}
}
