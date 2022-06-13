<?php 
/**
 * @package AxeptioWPPlugin
 */
namespace Inc\Api\Callbacks;
use Inc\Base\BaseController;

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
		echo __('Please enter your automation key here.', $this->text_domain);
	}
	public function xpwpSdkActiveSet()
	{
		$value = esc_attr( get_option( 'xpwp_sdk_active' ) );
		echo '<input type="checkbox" class="regular-text" name="xpwp_sdk_active" value="1" ' . ($value == 1 ? 'checked' : '') . ' placeholder="">';
	}
	public function xpwpClientIdSet()
	{
		$value = esc_attr( get_option( 'xpwp_client_id' ) );
		echo '<input type="text" class="regular-text" name="xpwp_client_id" value="' . $value . '" placeholder="">';
	}
	public function xpwpVersionSet()
	{
		$value = esc_attr( get_option( 'xpwp_version' ) );
		echo '<input type="text" class="regular-text" name="xpwp_version" value="' . $value . '" placeholder="">';
	}
}