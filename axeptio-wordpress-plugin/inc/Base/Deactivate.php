<?php
/**
 * @package AxeptioWordpressPlugin
 */

namespace IncludeAxeptioWordpressPlugin\Base;

class Deactivate
{
	public static function deactivate() {
		flush_rewrite_rules();
	}
}