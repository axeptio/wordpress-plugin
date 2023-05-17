<?php
return array(
	'wp_filter_mode' => 'blacklist',
	'wp_filter_list' => array(
		'wp_head > [ WP_Analytify, analytify_add_analytics_code ]',
		'wp_head > [ WP_Analytify, analytify_add_manual_analytics_code ]',
		'wp_enqueue_scripts > [ WP_Analytify, analytify_track_miscellaneous ]',
		'wp_enqueue_scripts > [ WP_Analytify, front_scripts ]',
		'init > init_gdpr_compliance',
	),
);
