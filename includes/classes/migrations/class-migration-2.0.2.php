<?php
namespace Axeptio\Migrations;

use Axeptio\Models\Plugins;

class Migration_2_0_2 implements \Axeptio\Contracts\Migration_Interface {
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
				"ALTER TABLE %s ALTER COLUMN cookie_widget_step SET DEFAULT 'wordpress';", // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.WP.CapitalPDangit.Misspelled
				$table // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			)
		);

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
