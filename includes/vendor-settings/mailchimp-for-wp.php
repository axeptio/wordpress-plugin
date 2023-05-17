<?php
return array(
	'wp_filter_mode'      => 'blacklist',
	'wp_filter_list'      => array(
		'init > [ MC4WP_Form_Asset_Manager, register_scripts ]',
		'wp_enqueue_scripts > [ MC4WP_Form_Asset_Manager, load_stylesheets ]',
		'wp_footer > [ MC4WP_Form_Asset_Manager, load_scripts ]',
		'mc4wp_output_form > [ MC4WP_Form_Asset_Manager, before_output_form ]',
	),
	'shortcode_tags_mode' => 'blacklist',
	'shortcode_tags_list' => array(
		'mc4wp_form',
	),
);
