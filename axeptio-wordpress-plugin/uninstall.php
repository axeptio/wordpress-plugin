<?php

use Axeptio\Admin;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;
$table_name_plugin = Admin::getPluginConfigurationsTable();
$wpdb->query( "DROP TABLE IF EXISTS $table_name_plugin" );

$table_name_widget = Admin::getWidgetConfigurationsTable();
$wpdb->query( "DROP TABLE IF EXISTS $table_name_widget" );

delete_option( Admin::OPTION_DB_VERSION );