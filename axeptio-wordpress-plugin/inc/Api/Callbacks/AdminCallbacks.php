<?php 
/**
 * @package AxeptioWPPlugin
 */
namespace Axpetio\SDKPlugin\Inc\Api\Callbacks;
use \Axpetio\SDKPlugin\Inc\Base\BaseController;

class AdminCallbacks extends BaseController{

	// Allowed HTML Tags for wp_kses
	private $allowedHtml = array(
		'input' => array(
			'type' => array(),
			'class' => array(),
			'name' => array(),
			'value' => array(),
			'id' => array(),
			'placeholder' => array(),
			'onchange' => array(),
			'checked' => array(),
		),
		'select' => array(
			'class' => array(),
			'name' => array(),
			'value' => array(),
			'id' => array(),
		),
		'option' => array(
			'value' => array()
		),
	);

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
		echo wp_kses('<input type="checkbox" class="regular-text" name="xpwp_sdk_active" value="1" ' . (esc_attr( get_option( 'xpwp_sdk_active' ) ) == 1 ? 'checked' : '') . ' placeholder="">', $this->allowedHtml);
	}
	public function xpwpClientIdSet()
	{
		echo wp_kses('<input type="text" class="regular-text" name="xpwp_client_id" id ="xpwp_client_id" value="' . esc_attr( get_option( 'xpwp_client_id' ) ) . '" placeholder="" onchange="loadVersionsOnChange()">', $this->allowedHtml);
	}
	public function xpwpVersionSet()
	{
		$escaped_value = esc_attr( get_option( 'xpwp_version' ) );
		echo wp_kses('<select name="xpwp_version" id="xpwp_version"><option value="' . $escaped_value . '">' . $escaped_value . '</option></select>', $this->allowedHtml);
	}
}