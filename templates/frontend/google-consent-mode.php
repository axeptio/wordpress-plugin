<?php if ( $data->active_google_consent_mode ) : ?>
	<script>
		window.gtag = window.gtag || function(){
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push(arguments);
		}
		gtag('set', 'developer_id.dNGFkYj', true);
		gtag('consent', 'default', {
				analytics_storage: "<?php echo esc_attr( $data->google_consent_mode_params['analytics_storage'] ); ?>",
				ad_storage: "<?php echo esc_attr( $data->google_consent_mode_params['ad_storage'] ); ?>",
				ad_user_data: "<?php echo esc_attr( $data->google_consent_mode_params['ad_user_data'] ); ?>",
				ad_personalization: "<?php echo esc_attr( $data->google_consent_mode_params['ad_personalization'] ); ?>",
			}
		);
	</script>
<?php endif; ?>
