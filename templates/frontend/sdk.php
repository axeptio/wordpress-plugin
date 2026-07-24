<?php defined( 'ABSPATH' ) || exit; ?>
<script>
	window.axeptioSettings = Axeptio_SDK;
	window.axeptioSettings.triggerGTMEvents = '<?php echo esc_js( \Axeptio\Plugin\Models\Settings::get_option( 'gtm_events', 'true' ) ); ?>';
	<?php if ( ! empty( $data->advanced_settings ) ) : ?>
		Object.assign(window.axeptioSettings, <?php echo wp_json_encode( $data->advanced_settings ); ?>);
	<?php endif; ?>
	(function (d, s) {
		var t = d.getElementsByTagName(s)[0],
			e = d.createElement(s);
		e.async = true;
		e.src = '<?php echo esc_attr( \Axeptio\Plugin\get_sdk_url() ); ?>';
		t.parentNode.insertBefore(e, t);
	})(document, 'script');
</script>
