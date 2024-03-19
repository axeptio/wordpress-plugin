<div class="bg-amber-100/60 rounded-lg overflow-hidden mt-6 shadow-md max-w-7xl mx-auto p-4 px-6 relative"
	x-data="noticeComponent(
			<?php echo esc_attr( wp_json_encode( array( 'nonce' => wp_create_nonce( 'wp_rest' ) ) ) ); ?>,
		)">
	<div class="flex gap-6">
		<button class="w-4 h-4 absolute top-4 right-4 p-0" id="axeptio-timeout-button">
			<img class="w-full h-full object-contain object-center"
				src="<?php echo esc_attr( \Axeptio\Plugin\get_img( 'close.svg' ) ); ?>" alt=""/>
		</button>
		<div class="h-32 aspect-square -translate-y-2 -ml-16">
			<img src="<?php echo esc_attr( \Axeptio\Plugin\get_img( 'review-icon.svg' ) ); ?>" alt=""/>
		</div>
		<div class="grow flex flex-col gap-6 justify-center">
			<div class="flex flex-col">
				<h3 class="text-lg font-semibold leading-7 text-gray-900">
					<?php esc_html_e( 'Axeptio aims for the moon, you look after the stars?', 'axeptio-wordpress-plugin' ); ?>&nbsp;⭐⭐⭐⭐⭐
				</h3>
				<p>
					<?php esc_html_e( 'If you like Axeptio, give us a rating to make us shine!', 'axeptio-wordpress-plugin' ); ?>
				</p>
			</div>
			<div>
				<ul role="list" class="flex flex-wrap items-center gap-3">
					<li>
						<a href="https://fr.wordpress.org/plugins/axeptio-sdk-integration/#reviews" target="_blank"
							class="rounded-full bg-amber-400 px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-900 hover:text-amber-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
							<?php esc_html_e( 'Review Axeptio plugin', 'axeptio-wordpress-plugin' ); ?>
						</a>
					</li>
					<li>
						<button class="p-0 hover:underline focus:underline" id="axeptio-disable-button">
							<?php echo esc_html__( 'Don\'t ask me anymore', 'axeptio-wordpress-plugin' ); ?>
						</button>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
