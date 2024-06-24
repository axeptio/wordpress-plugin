<div class="mt-6 lg:bg-[url('../../img/settings.svg')] bg-[length:40%_auto] bg-no-repeat bg-right-top">
	<form method="post"
		  action="options.php"
		  class="bg-[rgba(255,255,255,0.4)]  min-h-[500px]"
		  x-data='accountIDComponent(
					<?php

		  use Axeptio\Plugin\Models\Settings;

		  echo esc_attr(
			  wp_json_encode(
				  array(
					  'accountID' => Settings::get_option('client_id', ''),
					  'optionsJson' => Settings::get_option('xpwp_version_options', '', false),
					  'activeSDK' => (bool)Settings::get_option('sdk_active', '0'),
					  'activeGoogleConsentMode' => (bool)Settings::get_option('google_consent_mode', '0'),
					  'googleConsentModeParams' => (array)Settings::get_option(
						  'google_consent_params',
						  array(
							  'analytics_storage' => false,
							  'ad_storage' => false,
							  'ad_user_data' => false,
							  'ad_personalization' => false,
						  )
					  ),
					  'historizedVersions' => get_option('axeptio_versions', array()),
					  'selectedOption' => \Axeptio\Plugin\Models\Project_Versions::selected_versions(),
					  'sendDatas' => (bool)Settings::get_option('disable_send_datas', '0'),
				  )
			  )
		  );
		  ?>
					)'
	>
		<?php echo \Axeptio\Plugin\get_main_admin_tabs(); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="max-w-2xl">
			<?php
			settings_fields('xpwp_settings_group');
			do_settings_sections('axeptio-wordpress-plugin');
			submit_button();
			?>
		</div>
	</form>
</div>
