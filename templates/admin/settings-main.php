<div class="wrap">
	<h1 class="screen-reader-text">
		<?php esc_html_e( 'Axeptio', 'axeptio-wordpress-plugin' ); ?>
	</h1>
	<div id="axeptio-app" class="axeptio-app">
		<?php do_action( 'axeptio/before_main_setting_container' ); ?>
		<div class="axeptio-settings bg-white rounded-lg overflow-hidden mt-6 relative shadow-md max-w-7xl mx-auto">
			<?php do_action( 'axeptio/before_main_settings' ); ?>
			<div class="px-6 py-4 lg:pb-0">
				<div class="mt-6 mb-0 md:flex md:justify-between">
					<div class="md:inline-flex md:items-end">
						<img class="aspect-|14/3] w-40" src="<?php echo esc_attr( \Axeptio\Plugin\get_logo() ); ?>" alt="<?php esc_attr_e( 'Axeptio', 'axeptio-wordpress-plugin' ); ?>">
						<div class="mt-2 lg:mt-0 md:ml-6 text-base mb-1"><?php esc_html_e( 'Your Axeptio Settings', 'axeptio-wordpress-plugin' ); ?></div>
					</div>

					<div class="inline-flex items-center">
						<svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
							<path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z"></path>
						</svg>
						<a href="https://support.axeptio.eu/hc/en-gb/articles/17616260428561-Wordpress-integration" target="_blank" class="text-sm font-semibold"><?php esc_html_e( 'Documentation', 'axeptio-wordpress-plugin' ); ?></a>
					</div>
				</div>
				<div>
					<?php settings_errors(); ?>
				</div>
				<div class="mt-2 border-t border-gray-200">
					<?php \Axeptio\Plugin\get_template_part( 'admin/sections/main' ); ?>
				</div>
			</div>
			<?php do_action( 'axeptio/after_main_settings' ); ?>
		</div>
		<?php do_action( 'axeptio/after_main_setting_container' ); ?>
	</div>
</div>
