<?php
/**
 * Cookie Saver
 *
 * @package Axeptio
 */

namespace Axeptio\Frontend;

use Axeptio\Module;

class Cookie extends Module {
	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Registering the admin page.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'axeptio/ajax_nopriv_set_cookie', array( $this, 'setCookie' ) );
		add_action( 'axeptio/ajax_set_cookie', array( $this, 'setCookie' ) );
	}

	public function setCookie()
	{
// Check if the request is a POST request

		// Retrieve JSON payload from AJAX request
		$inputJSON = file_get_contents('php://input');
		$input = json_decode($inputJSON, TRUE); // Convert JSON to array

		// Validate and sanitize the input
		// TODO: Add validation and sanitation here

		// Extract data from input
		$userToken = $input['userToken'] ?? '';
		$userPreferences = $input['userPreferences'] ?? [];
		$consentInterfaceMetadata = $input['consentInterfaceMetadata'] ?? [];

		//$settings = json_decode(stripslashes($_REQUEST['settings']), true);
		$input = json_decode(stripslashes($_REQUEST['userPreferencesManager']), true);

		// Validate and sanitize the input
		// TODO: Add validation and sanitation here

		// Extract data from input
		$userToken = $input['$$token'] ?? '';
		$userPreferences = $input['userPreferencesManager'] ?? [];

		$consentInterfaceMetadata = $input['consentInterfaceMetadata'] ?? [];

		// Set 'axeptio_cookies' cookie
		$axeptioCookies = [
			'$$token' => $userToken,
			'$$date' => date('c'),
			'$$cookiesVersion' => true,
			'$$completed' => true
		];
		$axeptioCookies = array_merge($axeptioCookies, $userPreferences);
		setcookie('axeptio_cookies', json_encode($axeptioCookies), [
			'expires' => time() + 86400, // 1 day
			'path' => '/',
			'secure' => true,
			'httponly' => true,
			'samesite' => 'Strict'
		]);

		// Set 'axeptio_authorized_vendors' cookie
		$authorizedVendors = ',' . implode(',', array_keys(array_filter($userPreferences))) . ',';
		setcookie('axeptio_authorized_vendors', $authorizedVendors, [
			'expires' => time() + 86400, // 1 day
			'path' => '/',
			'secure' => true,
			'httponly' => true,
			'samesite' => 'Strict'
		]);

		// Set 'axeptio_all_vendors' cookie
		// TODO: Fetch all vendors list from your database or configuration
		$allVendors = ',vendor1,vendor2,vendorN,';
		setcookie('axeptio_all_vendors', $allVendors, [
			'expires' => time() + 86400, // 1 day
			'path' => '/',
			'secure' => true,
			'httponly' => true,
			'samesite' => 'Strict'
		]);

		// Send a response back to the client
		echo json_encode(['status' => 'success']);
	}
}
