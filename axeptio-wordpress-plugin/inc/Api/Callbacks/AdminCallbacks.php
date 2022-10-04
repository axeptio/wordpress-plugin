<?php 
/**
 * @package AxeptioWordpressPlugin
 */
namespace IncludeAxeptioWordpressPlugin\Api\Callbacks;
use IncludeAxeptioWordpressPlugin\Base\BaseController;

class AdminCallbacks extends BaseController{
	// Load Pages
	public function adminDashboard()
	{
		return require_once( "$this->plugin_path/templates/admin.php" );
	}
	// Admin Settings
	public function xpwpOptionsGroup( $input )
	{
		return $input;
	}
	public function xpwpAdminSection()
	{
		echo __('Please enter your automation key here.', 'axeptio-wordpress-plugin');
	}
	public function xpwpSdkActiveSet()
	{
		$escaped_value = esc_attr( get_option( 'xpwp_sdk_active' ) );
		echo '<input type="checkbox" class="regular-text" name="xpwp_sdk_active" value="1" ' . ($escaped_value == 1 ? 'checked' : '') . ' placeholder="">';
	}
	public function xpwpClientIdSet()
	{
		$escaped_value = esc_attr( get_option( 'xpwp_client_id' ) );
		echo '<input type="text" class="regular-text" name="xpwp_client_id" id ="xpwp_client_id" value="' . $escaped_value . '" placeholder="" onchange="loadVersionsOnChange()">';
	}
	public function xpwpVersionSet()
	{
		$escaped_value = esc_attr( get_option( 'xpwp_version' ) );
		echo '<select name="xpwp_version" id="xpwp_version"><option value="' . $escaped_value . '">' . $escaped_value . '</option></select>';
	}
}