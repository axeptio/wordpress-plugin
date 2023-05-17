<?php
return array(
	'wp_filter_mode'      => 'blacklist',
	'wp_filter_list'      => array(
		'wp_head > [ Leadin\\PageHooks, add_page_analytics ]',
		'wp_enqueue_scripts > [ Leadin\\PageHooks, add_frontend_scripts ]',
	),
	'shortcode_tags_mode' => 'blacklist',
	'shortcode_tags_list' => array(
		'hubspot',
	),
);
