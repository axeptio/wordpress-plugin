<script>
	window.axeptioSettings = Axeptio_SDK;
	(function (d, s) {
		var t = d.getElementsByTagName(s)[0],
			e = d.createElement(s);
		e.async = true;
		e.src = '<?php echo esc_attr( \Axeptio\Plugin\get_sdk_url() ); ?>';
		t.parentNode.insertBefore(e, t);
	})(document, 'script');
</script>
