<?php

use Axeptio\Admin;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;
$table_name = Admin::getPluginConfigurationsTable();
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
delete_option( Admin::OPTION_DB_VERSION );
