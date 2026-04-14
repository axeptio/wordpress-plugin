<?php
namespace Axeptio\Plugin\Migrations;

use Axeptio\Plugin\Models\Plugins;

class Migration_2_6_2 implements \Axeptio\Plugin\Contracts\Migration_Interface {
	/**
	 * Run the upgrade migration.
	 *
	 * @return void
	 */
	public function up() {
		global $wpdb;

		$table = $wpdb->prefix . Plugins::$table_name;

		// Check if columns don't already exist before adding them
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query, no caching needed.
		$shortcode_placeholder_button_text_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'shortcode_placeholder_button_text'",
				DB_NAME,
				$table
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Migration query, no caching needed.
		$shortcode_placeholder_hide_decoration_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'shortcode_placeholder_hide_decoration'",
				DB_NAME,
				$table
			)
		);

		// Add shortcode_placeholder_button_text column if it doesn't exist
		if ( empty( $shortcode_placeholder_button_text_exists ) ) {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Migration schema change, table name cannot be prepared.
			$wpdb->query(
				"ALTER TABLE `{$table}` ADD COLUMN `shortcode_placeholder_button_text` TEXT NULL AFTER `shortcode_placeholder_description`"
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		// Add shortcode_placeholder_hide_decoration column if it doesn't exist
		if ( empty( $shortcode_placeholder_hide_decoration_exists ) ) {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Migration schema change, table name cannot be prepared.
			$wpdb->query(
				"ALTER TABLE `{$table}` ADD COLUMN `shortcode_placeholder_hide_decoration` TINYINT(1) DEFAULT 0 AFTER `shortcode_placeholder_button_text`"
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
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

		// Check if columns exist before attempting to drop them (IF EXISTS not supported in MySQL 5.7)
		$shortcode_placeholder_hide_decoration_exists = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'shortcode_placeholder_hide_decoration'",
				DB_NAME,
				$table
			)
		);

		$shortcode_placeholder_button_text_exists = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'shortcode_placeholder_button_text'",
				DB_NAME,
				$table
			)
		);

		if ( ! empty( $shortcode_placeholder_hide_decoration_exists ) ) {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Migration schema change, table name cannot be prepared.
			$wpdb->query(
				"ALTER TABLE `{$table}` DROP COLUMN `shortcode_placeholder_hide_decoration`"
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		if ( ! empty( $shortcode_placeholder_button_text_exists ) ) {
			// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Migration schema change, table name cannot be prepared.
			$wpdb->query(
				"ALTER TABLE `{$table}` DROP COLUMN `shortcode_placeholder_button_text`"
			);
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
	}
}
