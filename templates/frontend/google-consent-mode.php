<?php defined( 'ABSPATH' ) || exit; ?>
<?php
/**
 * Google Consent Mode template.
 *
 * @package Axeptio
 */
?>
<?php if ( $data->active_google_consent_mode ) : ?>
	<script>
		window.dataLayer = window.dataLayer || [];
		window.gtag = window.gtag || function () {
			window.dataLayer.push( arguments );
		};
		gtag( 'set', 'developer_id.dNGFkYj', true );
		gtag( 'consent', 'default', {
			analytics_storage: "<?php echo esc_js( $data->google_consent_mode_params['analytics_storage'] ); ?>",
			ad_storage: "<?php echo esc_js( $data->google_consent_mode_params['ad_storage'] ); ?>",
			ad_user_data: "<?php echo esc_js( $data->google_consent_mode_params['ad_user_data'] ); ?>",
			ad_personalization: "<?php echo esc_js( $data->google_consent_mode_params['ad_personalization'] ); ?>",
			functionality_storage: "<?php echo esc_js( $data->google_consent_mode_params['functionality_storage'] ); ?>",
			personalization_storage: "<?php echo esc_js( $data->google_consent_mode_params['personalization_storage'] ); ?>",
			security_storage: "<?php echo esc_js( $data->google_consent_mode_params['security_storage'] ); ?>",
		} );
	</script>
<?php endif; ?>
