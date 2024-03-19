<?php
namespace Axeptio\Plugin\Migrations;

use Axeptio\Plugin\Models\Plugins;

class Migration_2_0_4 implements \Axeptio\Plugin\Contracts\Migration_Interface {
	/**
	 * Run the upgrade migration.
	 *
	 * @return void
	 */
	public function up() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table = $wpdb->prefix . Plugins::$table_name;

		$sql = "UPDATE `$table` SET `cookie_widget_step` = 'wordpress' WHERE cookie_widget_step IS NULL OR cookie_widget_step = 0;"; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled
		dbDelta( $sql );
	}

	/**
	 * Run the downgrade migration.
	 *
	 * @return void
	 */
	public function down() {    }
}
