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
		$shortcode_placeholder_button_text_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'shortcode_placeholder_button_text'",
				DB_NAME,
				$table
			)
		);

		$shortcode_placeholder_hide_decoration_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'shortcode_placeholder_hide_decoration'",
				DB_NAME,
				$table
			)
		);

		// Add shortcode_placeholder_button_text column if it doesn't exist
		if ( empty( $shortcode_placeholder_button_text_exists ) ) {
			$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				"ALTER TABLE `{$table}` ADD COLUMN `shortcode_placeholder_button_text` TEXT NULL AFTER `shortcode_placeholder_description`"
			);
		}

		// Add shortcode_placeholder_hide_decoration column if it doesn't exist
		if ( empty( $shortcode_placeholder_hide_decoration_exists ) ) {
			$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				"ALTER TABLE `{$table}` ADD COLUMN `shortcode_placeholder_hide_decoration` TINYINT(1) DEFAULT 0 AFTER `shortcode_placeholder_button_text`"
			);
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

		// Remove the columns if they exist
		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"ALTER TABLE `{$table}` DROP COLUMN IF EXISTS `shortcode_placeholder_hide_decoration`"
		);

		$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"ALTER TABLE `{$table}` DROP COLUMN IF EXISTS `shortcode_placeholder_button_text`"
		);
	}
}
