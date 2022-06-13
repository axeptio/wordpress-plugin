<div class="wrap">
	<h1><?php _e('Plugin Settings', 'axeptio-wordpress-plugin'); ?></h1>
	<?php settings_errors(); ?>

	<form method="post" action="options.php">
		<?php 
			settings_fields( 'xpwp_settings_group' );
			do_settings_sections( 'axeptio-wordpress-plugin' );
			submit_button();
		?>
	</form>

	
	<h3><?php _e('How To Integrate Axeptio SDK', 'axeptio-wordpress-plugin'); ?></h3>
	<p><?php _e('You need an <a href="https://admin.axeptio.eu">Axeptio account</a> to finish the configuration of this module', 'axeptio-wordpress-plugin'); ?></p>
	<iframe width="560" height="315" src="https://www.youtube.com/embed/K4TOrB7at0Y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>