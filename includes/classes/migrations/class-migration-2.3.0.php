<?php
namespace Axeptio\Migrations;

use Axeptio\Models\Plugins;

class Migration_2_3_0 implements \Axeptio\Contracts\Migration_Interface {
	/**
	 * Run the upgrade migration.
	 *
	 * @return void
	 */
	public function up() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table = $wpdb->prefix . Plugins::$table_name;

		$sql = "CREATE INDEX idx_plugin ON $table(plugin);"; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled
		dbDelta( $sql );
	}

	/**
	 * Run the downgrade migration.
	 *
	 * @return void
	 */
	public function down() {    }
}
