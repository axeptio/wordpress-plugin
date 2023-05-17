<?php
return array(
	'wp_filter_mode'      => 'blacklist',
	'wp_filter_list'      => array(
		'init > [ WpMatomo\\OptOut, load_block ]',
		'wp_enqueue_scripts > [ WpMatomo\\OptOut, load_scripts ]',
		'matomo_ecommerce_init',
		'template_redirect > [ WpMatomo\\Ecommerce\\EasyDigitalDownloads, on_product_view ]',
		'template_redirect > [ WpMatomo\\Ecommerce\\MemberPress, on_product_view ]',
		'wp_head > [ WpMatomo\\Ecommerce\\Woocommerce, maybe_track_order_complete ]',
		'wp_footer > [ WpMatomo\\TrackingCode, add_javascript_code ]',
		'wp_footer > [WpMatomo\\Ecommerce\Woocommerce, on_print_queues]',
		'wp_footer > [WpMatomo\\Ecommerce\EasyDigitalDownloads, on_print_queues]',
		'wp_footer > [WpMatomo\\Ecommerce\MemberPress, on_print_queues]',
		'wp_footer > [WpMatomo\\Ecommerce\MemberPress, on_order]',

	),
	'shortcode_tags_mode' => 'blacklist',
	'shortcode_tags_list' => array(
		'matomo_opt_out',
	),
);



