<div class="relative isolate overflow-hidden bg-amber-400/20">
	<svg class="w-full md:-mt-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path class="fill-white" d="M808,64.1c-205,11.1-205,11.1-410,6.5C195.4,66,193,66-5,74.9v-85.8h1215V67C1012.9,53,1010.3,53.1,808,64.1z"></path></svg>
	<img class="buddy-cookie hidden md:block" src="<?php echo esc_attr( \Axeptio\Plugin\get_img( 'buddy-cookie.svg' ) ); ?>" alt="" />
	<div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-12 px-6 pt-8 pb-12 sm:px-16 md:pt-0 md:px-36 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
		<div class="text-center">
			<h3 class="mx-auto font-serif max-w-2xl text-xl font-bold tracking-tight text-gray-900 sm:text-xl">
				<?php esc_html_e( "Don't have a Axeptio account?", 'axeptio-wordpress-plugin' ); ?>
			</h3>
			<p class="mx-auto mt-2 max-w-xl text-md leading-5 text-gray-900">
				<?php esc_html_e( 'You need an Axeptio account to finish the configuration of this module', 'axeptio-wordpress-plugin' ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</p>
			<div class="mt-6 flex flex-col items-center justify-center gap-y-2">
				<a href="https://admin.axeptio.eu/#signup" target="_blank" class="rounded-full bg-amber-400 px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-900 hover:text-amber-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
					<?php esc_html_e( 'I create an account now', 'axeptio-wordpress-plugin' ); ?>
				</a>
				<a href="https://www.axeptio.eu/fr/cookie-widget" target="_blank" class="text-xs font-semibold leading-6 text-gray-900">
					<?php esc_html_e( 'Learn more', 'axeptio-wordpress-plugin' ); ?> <span aria-hidden="true">→</span>
				</a>
			</div>
		</div>
		<div class="text-center">
			<h3 class="mx-auto font-serif max-w-2xl text-xl font-bold tracking-tight text-gray-900 sm:text-xl">
				<?php esc_html_e( 'You have already a Axeptio account?', 'axeptio-wordpress-plugin' ); ?>
			</h3>
			<p class="mx-auto mt-2 max-w-xl text-md leading-5 text-gray-900">
				<?php esc_html_e( 'Go to your personal dashboard to retrieve the project ID you want to use', 'axeptio-wordpress-plugin' ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</p>
			<div class="mt-6 flex flex-col items-center justify-center gap-y-3">
				<a href="https://admin.axeptio.eu/" target="_blank" class="rounded-full bg-amber-400 px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-900 hover:text-amber-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
					<?php esc_html_e( 'I login to my account', 'axeptio-wordpress-plugin' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>
