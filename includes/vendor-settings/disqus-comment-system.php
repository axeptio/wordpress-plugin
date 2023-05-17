<?php
return array(
	'wp_filter_mode' => 'blacklist',
	'wp_filter_list' => array(
		'comments_number > [Disqus_Public, dsq_comments_link_template]',
		'comments_template > [Disqus_Public, dsq_comments_template]',
		'wp_enqueue_scripts > [Disqus_Public, enqueue_comment_count]',
		'wp_enqueue_scripts > [Disqus_Public, enqueue_comment_embed]',
		'show_user_profile > [Disqus_Public, dsq_close_window_template]',
	),
);
