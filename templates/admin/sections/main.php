<div class="lg:grid lg:grid-cols-12 lg:gap-x-8">
	<div class="lg:col-span-6 lg:col-start-1 mt-6">
		<form method="post"
				action="options.php"
				x-data='accountIDComponent(
					<?php

					use Axeptio\Plugin\Models\Settings;

					echo esc_attr(
							wp_json_encode(
								array(
									'accountID'      => Settings::get_option( 'client_id', '' ),
									'optionsJson'    => Settings::get_option( 'xpwp_version_options', '', false ),
									'activeSDK'      => (bool) Settings::get_option( 'sdk_active', '0' ),
									'selectedOption' => \Axeptio\Plugin\Models\Project_Versions::selected_versions(),
									'sendDatas'      => (bool) Settings::get_option( 'disable_send_datas', '0' ),
								)
							)
						);
					?>
					)'
		>
			<?php echo \Axeptio\Plugin\get_main_admin_tabs(); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php
			settings_fields( 'xpwp_settings_group' );
			do_settings_sections( 'axeptio-wordpress-plugin' );
			submit_button();
			?>
		</form>
	</div>
	<div class="mt-6 lg:col-span-8 lg:col-start-7 lg:row-start-8">
		<h2 class="text-xl font-medium text-gray-900 mb-4">
			<?php esc_html_e( 'How To Integrate Axeptio?', 'axeptio-wordpress-plugin' ); ?>
		</h2>
		<iframe class="w-full aspect-[16/9] border-0" src="https://www.youtube-nocookie.com/embed/qrt9YRO-0xc" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
	</div>
</div>
