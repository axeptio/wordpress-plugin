<?php


use Axeptio\Widget_Configurations_List_Table;

?>
<div class="wrap">
  <h1><?= __('Widget configuration', 'axeptio-wordpress-plugin')?></h1>
  <a href="<?= admin_url("admin.php?page=axeptio-widget-configurations&sub=form") ?>" class="page-title-action aria-button-if-js" role="button" aria-expanded="false">
	  <?= __('Add New', 'axeptio-wordpress-plugin')?>
  </a>
  <hr class="wp-header-end">
  <form action="<?= admin_url("admin.php?page=axeptio-widget-configurations") ?>" method="post">
	  <?php

	  $table = new Widget_Configurations_List_Table();
	  $table->prepare_items();
	  $table->display();

	  ?></form>
</div>
