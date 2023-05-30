<div class="wrap">
	<h1 class="screen-reader-text">
		<?php esc_html_e( 'Axeptio', 'axeptio-wordpress-plugin' ); ?>
	</h1>
	<div id="axeptio-app" class="axeptio-app">
		<?php do_action( 'axeptio/before_main_setting_container' ); ?>
		<div class="axeptio-settings bg-white rounded-lg overflow-hidden mt-6 relative shadow-md max-w-7xl mx-auto">
			<?php do_action( 'axeptio/before_main_settings' ); ?>
			<div class="px-6 py-4 lg:pb-0">
				<div class="mt-6 mb-0">
					<div class="md:inline-flex md:items-end">
						<img class="aspect-|14/3] w-40" src="<?php echo esc_attr( \Axeptio\get_logo() ); ?>" alt="<?php esc_attr_e( 'Axeptio', 'axeptio-wordpress-plugin' ); ?>">
						<div class="mt-2 lg:mt-0 md:ml-6 text-base mb-1"><?php esc_html_e( 'Your Axeptio Settings', 'axeptio-wordpress-plugin' ); ?></div>
					</div>
					<?php settings_errors(); ?>
				</div>
				<div class="mt-2 border-t border-gray-200">
					<?php \Axeptio\get_template_part( 'admin/sections/main' ); ?>
				</div>
			</div>
			<?php do_action( 'axeptio/after_main_settings' ); ?>
		</div>
		<?php do_action( 'axeptio/after_main_setting_container' ); ?>
	</div>
</div>
