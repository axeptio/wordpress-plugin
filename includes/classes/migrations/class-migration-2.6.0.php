<?php
namespace Axeptio\Plugin\Migrations;

use Axeptio\Plugin\Models\Plugins;

class Migration_2_6_0 implements \Axeptio\Plugin\Contracts\Migration_Interface {
	/**
	 * Run the upgrade migration.
	 *
	 * @return void
	 */
	public function up() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table = $wpdb->prefix . Plugins::$table_name;

		// Check if columns don't already exist before adding them.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query, no caching needed.
		$shortcode_placeholder_title_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'shortcode_placeholder_title'",
				DB_NAME,
				$table
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query, no caching needed.
		$shortcode_placeholder_description_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'shortcode_placeholder_description'",
				DB_NAME,
				$table
			)
		);

		// Add shortcode_placeholder_title column if it doesn't exist.
		if ( empty( $shortcode_placeholder_title_exists ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Migration schema change, table name cannot be prepared.
			$wpdb->query( "ALTER TABLE `{$table}` ADD COLUMN `shortcode_placeholder_title` TEXT NULL AFTER `shortcode_tags_placeholder`" );
		}

		// Add shortcode_placeholder_description column if it doesn't exist.
		if ( empty( $shortcode_placeholder_description_exists ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Migration schema change, table name cannot be prepared.
			$wpdb->query( "ALTER TABLE `{$table}` ADD COLUMN `shortcode_placeholder_description` TEXT NULL AFTER `shortcode_placeholder_title`" );
		}
	}

	/**
	 * Run the downgrade migration.
	 *
	 * @return void
	 */
	public function down() {
		global $wpdb;

		$table = $wpdb->prefix . Plugins::$table_name;

		// Remove the columns if they exist.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Migration schema change, table name cannot be prepared.
		$wpdb->query( "ALTER TABLE `{$table}` DROP COLUMN IF EXISTS `shortcode_placeholder_description`" );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Migration schema change, table name cannot be prepared.
		$wpdb->query( "ALTER TABLE `{$table}` DROP COLUMN IF EXISTS `shortcode_placeholder_title`" );
	}
}
