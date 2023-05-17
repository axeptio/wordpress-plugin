<?php
return array(
	'wp_filter_mode' => 'blacklist',
	'wp_filter_list' => array(
		'> monsterinsights_tracking_script',
		'wp_enqueue_scripts > [ MonsterInsights_Analytics_Events, output_javascript ]',
		'wp_enqueue_scripts > [ MonsterInsights_Gtag_Events, output_javascript ]',
		'wp_enqueue_scripts > [ MonsterInsights_Popular_Posts, maybe_load_ajaxify_script ]',
	),
);
