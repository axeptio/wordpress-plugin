<?php
namespace Axeptio\Migrations;

use Axeptio\Models\Plugins;
use Axeptio\Models\Settings;

class Migration_2_0_0 implements \Axeptio\Contracts\Migration_Interface {
	/**
	 * Run the upgrade migration.
	 *
	 * @return void
	 */
	public function up() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table           = Plugins::$table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table  (
                -- primary key if composed of the plugin name and the axeptio_configuration_id
                -- it would maybe be better to use a unique index and auto_increment column?
	            `plugin` varchar(80) NOT NULL,
	            `axeptio_configuration_id` varchar(50) NOT NULL,
	            `enabled` BOOLEAN NOT NULL DEFAULT 0,
	            -- settings that tell to the Axeptio WP Plugin how to behave with this plugin's filters
	            `wp_filter_mode` ENUM('none', 'all', 'blacklist', 'whitelist') NOT NULL DEFAULT 'all',
	            `wp_filter_list` TEXT NULL,
	            `wp_filter_store_output` BOOLEAN NOT NULL DEFAULT 0,
	            `wp_filter_reload_page_after_consent` ENUM('no', 'ask', 'yes') NOT NULL DEFAULT 'no',
                -- settings that tell to the Axeptio WP Plugin how to behave with this plugin's shortcodes
	            `shortcode_tags_mode` ENUM('none', 'all', 'blacklist', 'whitelist') NOT NULL DEFAULT 'all',
	            `shortcode_tags_list` TEXT NULL,
	            `shortcode_tags_placeholder` TEXT,
	            -- info about that will be displayed in the widget
	            `vendor_id` varchar(24) NULL,
	            `vendor_title` TEXT NOT NULL,
	            `vendor_shortDescription` TEXT NOT NULL,
	            `vendor_longDescription` TEXT NOT NULL,
	            `vendor_policyUrl` TEXT NOT NULL,
	            `vendor_image` TEXT NOT NULL,
    			`cookie_widget_step` VARCHAR (255) NULL,
	            PRIMARY KEY  (`plugin`, `axeptio_configuration_id`)
		    ) $charset_collate;";

		dbDelta( $sql );
	}

	/**
	 * Run the downgrade migration.
	 *
	 * @return void
	 */
	public function down() {    }
}
