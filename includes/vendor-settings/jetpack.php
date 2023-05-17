<?php
return array(
	'wp_filter_mode' => 'blacklist',
	'wp_filter_list' => array(
		'wp_enqueue_scripts > [ Tracking_Pixel, enqueue_stats_script ]',
		'wp_footer > [ Tracking_Pixel, add_amp_pixel ]',
		'web_stories_print_analytics > [ Tracking_Pixel, add_amp_pixel ]',
		'wp_enqueue_scripts > [Jetpack_WooCommerce_Analytics_Universal, enqueue_tracking_script]',
		'wp_head > [ WordAds, insert_head_meta ]',
		'wp_head > [ WordAds, insert_head_iponweb ]',
		'wp_enqueue_scripts > [ WordAds, enqueue_scripts ]',
		'wp_enqueue_scripts > [ Jetpack_Google_Translate_Widget, enqueue_scripts ]',
		'wp_enqueue_scripts > [ WPCOM_Widget_Facebook_LikeBox, enqueue_scripts ]',
		'wp_enqueue_scripts > [ Jetpack_EU_Cookie_Law_Widget, enqueue_scripts ]',
		'wordads_ads_txt > [ WordAds, insert_custom_adstxt ]',
		'wp_enqueue_scripts > twitter_timeline_js',
		'wp_enqueue_scripts > [Jetpack_Twitter_Timeline_Widget, enqueue_scripts]',
	),
);
