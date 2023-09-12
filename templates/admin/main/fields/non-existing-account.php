<div class="mt-2 text-sm text-red-700">
	<p><?php esc_html_e( 'If you are certain that your project exists, please follow these steps:', 'axeptio-wordpress-plugin' ); ?></p>
</div>
<div class="mt-2 text-sm text-red-700">
	<ul role="list" class="list-disc space-y-1 pl-5">
		<li><?php esc_html_e( 'Go to the Axeptio interface.', 'axeptio-wordpress-plugin' ); ?></li>
		<li><?php esc_html_e( 'Publish your project.', 'axeptio-wordpress-plugin' ); ?></li>
		<li><?php esc_html_e( 'Return here and refresh the configuration.', 'axeptio-wordpress-plugin' ); ?></li>
		<li>
			<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: the url of the Axeptio help guide */
						__( "If the issue persists, please don't hesitate to consult <a target=\"_blank\" class=\"underline hover:no-underline\" href=\"%s\">our help guide.</a>", 'axeptio-wordpress-plugin' ),
						esc_url( __( 'https://support.axeptio.eu/hc/en-gb', 'axeptio-wordpress-plugin' ) )
					)
				);
				?>
		</li>
	</ul>
</div>
