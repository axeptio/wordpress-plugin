<?php
return array(
	'wp_filter_mode' => 'blacklist',
	'wp_filter_list' => array(
		'> exactmetrics_tracking_script',
		'wp_enqueue_scripts > [ ExactMetrics_Analytics_Events, output_javascript ]',
		'wp_enqueue_scripts > [ ExactMetrics_Gtag_Events, output_javascript ]',
		'wp_enqueue_scripts > [ ExactMetrics_Popular_Posts, maybe_load_ajaxify_script ]',
	),
);
