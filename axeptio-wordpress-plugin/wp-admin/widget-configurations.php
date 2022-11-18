<?php


use Axeptio\Widget_Configurations_List_Table;

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?= __( 'Widget custom steps', 'axeptio-wordpress-plugin' ) ?></h1>
    <a href="<?= admin_url( "admin.php?page=axeptio-widget-configurations&sub=form" ) ?>"
       class="page-title-action aria-button-if-js" role="button" aria-expanded="false">
		<?= __( 'Add New', 'axeptio-wordpress-plugin' ) ?>
    </a>
    <p class="description">
        The Axeptio WordPress SDK can add custom steps in your cookie consent widget. These steps will be available
        when you create a plugin configuration. If no plugin or vendor is assigned to a step you have created here,
        the step will not be shown.
    </p>
    <hr class="wp-header-end">
    <form action="<?= admin_url( "admin.php?page=axeptio-widget-configurations" ) ?>" method="post">
		<?php

		$table = new Widget_Configurations_List_Table();
		$table->prepare_items();
		$table->display();

		?></form>
</div>
