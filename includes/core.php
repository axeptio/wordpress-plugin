<?php
/**
 * Core plugin functionality.
 *
 * @package Axeptio
 */

namespace Axeptio;

use Axeptio\Init\Activate;
use Axeptio\Init\Activation_Hook;
use Axeptio\ModuleInitialization;
use Axeptio\Utils\WP_Migration_Manager;
use \WP_Error;
use Axeptio\Utility;


/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = fn( $func) => __NAMESPACE__ . "\\$func";

	add_action( 'init', $n( 'migrate' ) );
	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ), apply_filters( 'axeptio/init_priority', 8 ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );

	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );

	do_action( 'axeptio/loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'axeptio/plugin_locale', get_locale(), 'axeptio-wordpress-plugin' );
	load_textdomain( 'axeptio-wordpress-plugin', WP_LANG_DIR . '/axeptio-wordpress-plugin/axeptio-wordpress-plugin-' . $locale . '.mo' );
	load_plugin_textdomain( 'axeptio-wordpress-plugin', false, plugin_basename( XPWP_PATH ) . '/languages/' );
}

/**
 * Run the available migrations.
 *
 * @return void
 */
function migrate() {
	$migration_manager = new WP_Migration_Manager();
	$migration_manager->migrate();
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'axeptio/before_init' );
	// If the composer.json isn't found, trigger a warning.
	if ( ! file_exists( XPWP_PATH . 'composer.json' ) ) {
		add_action(
			'admin_notices',
			function() {
				$class = 'notice notice-error';
				/* translators: %s: the path to the plugin */
				$message = sprintf( __( 'The composer.json file was not found within %s. No classes will be loaded.', 'axeptio-wordpress-plugin' ), XPWP_PATH );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			}
		);
		return;
	}

	Module_Initialization::instance()->init_classes();
	do_action( 'axeptio/init' );
}

/**
 * Activate the plugin
 *
 * @return void
 */
function activate() {
	( new Activation_Hook() )->maybe_redirect_to_settings_page();
	// First load the init scripts in case any rewrite functionality is being loaded.
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {
}


/**
 * The list of knows contexts for enqueuing scripts/styles.
 *
 * @return array
 */
function get_enqueue_contexts() {
	return array( 'admin', 'frontend' );
}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $script Script file name (no .js extension).
 * @param string $context Context for the script ('admin', 'frontend', or 'shared').
 *
 * @return string|WP_Error URL
 */
function script_url( $script, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in Axeptio script loader.' );
	}

	return XPWP_URL . "dist/js/${script}.js";
}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension).
 * @param string $context Context for the script ('admin', 'frontend', or 'shared').
 *
 * @return string|WP_Error URL
 */
function style_url( $stylesheet, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in Axeptio stylesheet loader.' );
	}

	return XPWP_URL . "dist/css/${stylesheet}.css";
}


/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {
	wp_enqueue_script(
		'axeptio/main',
		script_url( 'main', 'admin' ),
		Utility\get_asset_info( 'admin', 'dependencies' ),
		Utility\get_asset_info( 'admin', 'version' ),
		true
	);
}

/**
 * Enqueue styles for admin.
 *
 * @return void
 */
function admin_styles() {
	wp_enqueue_style(
		'axeptio/main',
		style_url( 'main', 'admin' ),
		array(),
		Utility\get_asset_info( 'shared', 'version' ),
	);

	wp_localize_script(
		'axeptio/main',
		'Axeptio',
		array(
			'errors' => array(
				'empty_account_id'        => __( 'Please enter an account ID', 'axeptio-wordpress-plugin' ),
				'non_existing_account_id' => __( "This account doesn't exist", 'axeptio-wordpress-plugin' ),
				'verification_error'      => __( 'Error verifying account ID. Try Again.', 'axeptio-wordpress-plugin' ),
			),
		)
		);
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag; // _doing_it_wrong()?
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;
}
