<?php


use Axeptio\Admin;
use Axeptio\Plugin_Configurations_List_Table;

$admin = Admin::instance();
$pluginsConfigurations = $admin->fetchPluginsConfigurations();
$plugins = get_plugins();

// Creating the rows list for the table
foreach ( $plugins as $path => &$plugin ) {
	$plugin['Configurations'] = array_filter( $pluginsConfigurations, function ( $config ) use ( $path, $plugin ) {
		return ( $config->plugin == dirname($path) );
	} );
}

?>
<div class="wrap">
    <h1 class="wp-heading-inline">Axeptio Plugins Configurations</h1>
    <a href="<?= admin_url("admin.php?page=axeptio-plugin-configurations&sub=form") ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">
        Add New
    </a>
    <hr class="wp-header-end">
    <form action="<?= admin_url("admin.php?page=axeptio-plugin-configurations") ?>" method="post">
    <?php

    $table = new Plugin_Configurations_List_Table();
    $table->prepare_items();
    $table->display();

    ?></form>
</div>
