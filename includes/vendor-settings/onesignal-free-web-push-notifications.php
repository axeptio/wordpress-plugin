<?php
return array(
	'wp_filter_mode' => 'blacklist',
	'wp_filter_list' => array(
		'wp_head > [ OneSignal_Public, onesignal_header ]',
		'wp_enqueue_scripts > [ OneSignal_Public, onesigal_amp_style ]',
	),
);
