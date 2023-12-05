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

		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			sprintf(
				"ALTER TABLE %s ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY;", // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.WP.CapitalPDangit.Misspelled
				$table // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			)
		);

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
