<?php
/**
 *  Models init
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Models;

use Axeptio\Plugin\Module;

class Models extends Module {
	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register models utilities.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_loaded', array( $this, 'register_models_tables' ) );
	}

	/**
	 * Register the models tables.
	 *
	 * @return void
	 */
	public function register_models_tables() {
		Plugins::register_plugin_configuration_table();
	}
}
