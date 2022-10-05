<?php
/**
 * @package AxeptioWPPlugin
 */

namespace Axpetio\SDKPlugin\Inc\Base;

class Deactivate
{
	public static function deactivate() {
		flush_rewrite_rules();
	}
}