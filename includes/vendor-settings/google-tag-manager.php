<?php
return array(
	'wp_filter_mode' => 'blacklist',
	'wp_filter_list' => array(
		'wp_head > [google_tag_manager, print_tag]',
		'wp_body_open > [google_tag_manager, print_noscript_tag]',
		'genesis_before > [google_tag_manager, print_noscript_tag]',
		'tha_body_top > [google_tag_manager, print_noscript_tag]',
		'body_top > [google_tag_manager, print_noscript_tag]',
		'wp_footer > [google_tag_manager, print_noscript_tag]',
	),
);
