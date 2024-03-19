<?php
/**
	Plugin Name: Axeptio
	Plugin URI: https://www.axeptio.eu/
	Description: Axeptio allows you to make your website compliant with GDPR.
	Version: 2.3.1
	Author: axeptio
	License: GPLv3
	License URI: https://www.gnu.org/licenses/gpl-3.0.html
	Text Domain: axeptio-wordpress-plugin
	Domain Path: /languages
 **/

// Useful global constants.
use Axeptio\Plugin\Models\Settings;

define( 'XPWP_VERSION', '2.3.1' );
define( 'XPWP_URL', plugin_dir_url( __FILE__ ) );
define( 'XPWP_PATH', plugin_dir_path( __FILE__ ) );
define( 'XPWP_BASENAME', plugin_basename( __FILE__ ) );
define( 'XPWP_INC', XPWP_PATH . 'includes/' );
define( 'XPWP_MIN_PHP_VERSION', '7.4' );

if ( ! defined( 'DS' ) ) {
	define( 'DS', DIRECTORY_SEPARATOR ); // PHPCS:Ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
}

if ( version_compare( PHP_VERSION, XPWP_MIN_PHP_VERSION, '<' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	deactivate_plugins( XPWP_BASENAME );

	$axeptio_error_message = '<h1>' . esc_attr__( 'Unable to activate Axeptio', 'axeptio-wordpress-plugin' ) . '</h1>';

	$axeptio_error_message .= '<p>' . sprintf(
			/* translators: %1$s: current php version min and %2$s version of php required */
			esc_attr__( 'Your server is currently running PHP %1$s. The Axeptio plugin requires at least PHP %2$s. Please upgrade your PHP version to use this plugin.', 'axeptio-wordpress-plugin' ),
		PHP_VERSION,
		'7.4'
	) . '</p>';
	$axeptio_error_message .= sprintf( '<p><a class="button button-large" href="%1$s">%2$s</a></p>', esc_url( get_admin_url( null, 'plugins.php' ) ), __( 'Return to plugins pages', 'axeptio-wordpress-plugin' ) );

	wp_die( ( $axeptio_error_message ) ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}

$xpwp_is_local_envenv = in_array( wp_get_environment_type(), array( 'local', 'development' ), true );
$xpwp_is_local_envurl = strpos( home_url(), '.test' ) || strpos( home_url(), '.docker.localhost' );
$xpwp_is_local        = $xpwp_is_local_envenv || $xpwp_is_local_envurl;

// Require Composer autoloader if it exists.
if ( file_exists( XPWP_PATH . 'vendor/autoload.php' ) ) {
	require_once XPWP_PATH . 'vendor/autoload.php';
	require_once XPWP_PATH . 'includes/wpcs-autoload.php';
}


if ( ! (bool) Settings::get_option( 'disable_send_datas', false ) ) {
	Sentry\init( array( 'dsn' => 'https://f7fe61f60f424acba143522d108ebe4a@o561678.ingest.sentry.io/4505249684914176' ) );
}

// Activation/Deactivation.
register_activation_hook( __FILE__, '\Axeptio\Plugin\activate' );
register_deactivation_hook( __FILE__, '\Axeptio\Plugin\deactivate' );

// Bootstrap.
Axeptio\Plugin\setup();
