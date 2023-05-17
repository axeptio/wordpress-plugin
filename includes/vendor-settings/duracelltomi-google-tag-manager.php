<?php
return array(
	'wp_filter_mode' => 'blacklist',
	'wp_filter_list' => array(
		'wp_head > gtm4wp_wp_header_begin',
		'wp_head > gtm4wp_wp_header_top',
		'wp_footer > gtm4wp_wp_footer',
		'wp_loaded > gtm4wp_wp_loaded',
	),
);
