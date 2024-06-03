<div class="inline-flex items-center w-full relative">
	<label for="xpwp_google_consent_mode"
			class="relative inline-flex h-7 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 bg-gray-400"
			role="switch" aria-checked="true" :aria-checked="activeGoogleConsentMode.toString()" x-state:on="Enabled"
			x-state:off="Not Enabled"
			:class="{ 'bg-amber-400': activeGoogleConsentMode, 'bg-gray-400': !(activeGoogleConsentMode) }">
		<span
			aria-hidden="true"
			x-state:on="Enabled"
			x-state:off="Not Enabled"
			class="pointer-events-none flex items-center justify-center shadow-md relative -left-1 -top-1 inline-block h-8 w-8 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"
			:class="{ 'translate-x-8': activeGoogleConsentMode, 'translate-x-0': !(activeGoogleConsentMode) }"
		>
			<svg class="hidden fill-gray-400"
				:class="{ 'hidden': activeGoogleConsentMode, 'block': !(activeGoogleConsentMode) }"
				xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<g>
					<path
						d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41z"></path>
				</g>
			</svg>
			<svg class="hidden fill-amber-400"
				:class="{ 'block': activeGoogleConsentMode, 'hidden': !(activeGoogleConsentMode) }"
				xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<path d="M9,16.17L4.83,12l-1.42,1.41L9,19L21,7l-1.41-1.41L9,16.17z"></path>
			</svg>
		</span>
	</label>
	<div class="ml-5">
		<label
			for="xpwp_google_consent_mode"><?php echo esc_html__( 'Enable Google Consent Mode V2?', 'axeptio-wordpress-plugin' ); ?></label>
	</div>
	<input type="checkbox" @change="activeGoogleConsentMode = !activeGoogleConsentMode"
			class="appearance-none w-full h-full active:outline-none focus:outline-none opacity-0 absolute -left-full top-0"
			id="xpwp_google_consent_mode" name="axeptio_settings[google_consent_mode]"
			value="1" <?php echo (bool) \Axeptio\Plugin\get_option( 'google_consent_mode', '0' ) ? 'checked' : ''; ?>
			placeholder="">
</div>

<?php
$axeptio_google_consent_params = (array) \Axeptio\Plugin\get_option( 'google_consent_params', array() );
$axeptio_google_params_list    = array(
	array(
		'label'       => esc_html__( 'Analytics storage', 'axeptio-wordpress-plugin' ),
		'description' => esc_html__( 'Allow Google Analytics to measure how visitors use the site to enhance functionality and service.', 'axeptio-wordpress-plugin' ),
		'name'        => 'analytics_storage',
	),
	array(
		'label'       => esc_html__( 'Ad Storage', 'axeptio-wordpress-plugin' ),
		'description' => esc_html__( "Permit Google to save advertising information on visitors' devices for better ad relevance.", 'axeptio-wordpress-plugin' ),
		'name'        => 'ad_storage',
	),
	array(
		'label'       => esc_html__( 'Ad User Data', 'axeptio-wordpress-plugin' ),
		'description' => esc_html__( "Share visitors' activity data with Google for targeted advertising.", 'axeptio-wordpress-plugin' ),
		'name'        => 'ad_user_data',
	),
	array(
		'label'       => esc_html__( 'Ad Personalization', 'axeptio-wordpress-plugin' ),
		'description' => esc_html__( 'Customize the ad experience by allowing Google to personalize the ads that visitors see.', 'axeptio-wordpress-plugin' ),
		'name'        => 'ad_personalization',
	),
);

?>
<div class="mt-2 text-sm text-gray-900" x-show="activeGoogleConsentMode">
	<div class="font-medium mt-3 mb-1"><?php echo esc_html__( 'Default settings for consent mode', 'axeptio-wordpress-plugin' ); ?></div>
	<div class="text-gray-500 text-xs mb-4">
		<?php echo esc_html__( 'These consent signals will be sent at page load to tell Google services how they should handle data before consent is granted or denied.', 'axeptio-wordpress-plugin' ); ?>
	</div>
	<ul role="list" class="divide-y divide-gray-100 rounded-md border border-gray-200">
		<?php foreach ( $axeptio_google_params_list as $axeptio_google_param ) : ?>
			<li class="flex items-center justify-between py-2 pl-4 pr-5 text-sm leading-6">
					<?php
						\Axeptio\Plugin\get_template_part(
							'admin/common/fields/toggle',
							array(
								'label'        => $axeptio_google_param['label'],
								'name'         => 'axeptio_settings[google_consent_params][' . esc_attr( $axeptio_google_param['name'] ) . ']',
								'description'  => $axeptio_google_param['description'],
								'id'           => 'xpwp_' . esc_attr( $axeptio_google_param['name'] ),
								'alpine_state' => 'googleConsentModeParams.' . esc_attr( $axeptio_google_param['name'] ),
								'checked'      => isset( $axeptio_google_consent_params[ esc_attr( $axeptio_google_param['name'] ) ] ) && '1' === $axeptio_google_consent_params[ esc_attr( $axeptio_google_param['name'] ) ],
							),
						);
					?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
