<?php 
/**
 * @package AxeptioWPPlugin
 */
namespace Axpetio\SDKPlugin\Inc\Api\Callbacks;
use \Axpetio\SDKPlugin\Inc\Base\BaseController;

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
		echo esc_attr( __('Please enter your automation key here.', 'axeptio-wordpress-plugin'));
	}
	public function xpwpSdkActiveSet()
	{
		$value = esc_attr( get_option( 'xpwp_sdk_active' ) );
		echo '<input type="checkbox" class="regular-text" name="xpwp_sdk_active" value="1" ' . ($value == 1 ? 'checked' : '') . ' placeholder="">';
	}
	public function xpwpClientIdSet()
	{
		$value = esc_attr( get_option( 'xpwp_client_id' ) );
		echo '<input type="text" class="regular-text" name="xpwp_client_id" id ="xpwp_client_id" value="' . $value . '" placeholder="" onchange="loadVersionsOnChange()">';
	}
	public function xpwpVersionSet()
	{
		$value = esc_attr( get_option( 'xpwp_version' ) );
		echo '<select name="xpwp_version" id="xpwp_version"><option value="' . $value . '">' . $value . '</option></select>';
	}
}