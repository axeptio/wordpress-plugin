<?php
/**
 * Core plugin functionality.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin;

defined( 'ABSPATH' ) || exit;

use Axeptio\Plugin\Init\Activate;
use Axeptio\Plugin\Init\Activation_Hook;
use Axeptio\Plugin\Utils\Flash_Vars;
use Axeptio\Plugin\Utils\WP_Migration_Manager;
use WP_Error;
use function Axeptio\Plugin\Utility\get_asset_info;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = fn( $func ) => __NAMESPACE__ . "\\$func";

	add_action( 'init', $n( 'migrate' ) );
	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ), apply_filters( 'axeptio/init_priority', 8 ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );

	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );

	do_action( 'axeptio/loaded' );

	add_action(
		'init',
		function () {
			global $wpdb;
			$table          = 'axeptio_plugin_configuration';
			$wpdb->$table   = $wpdb->prefix . $table;
			$wpdb->tables[] = $table;
		}
	);
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'axeptio/plugin_locale', get_locale(), 'axeptio-sdk-integration' );
	load_plugin_textdomain( 'axeptio-sdk-integration', false, plugin_basename( XPWP_PATH ) . '/languages/' );
	if ( ! is_textdomain_loaded( 'axeptio-sdk-integration' ) ) {
		load_textdomain( 'axeptio-sdk-integration', XPWP_PATH . 'languages/axeptio-sdk-integration-' . $locale . '.mo' );
	}
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
			function () {
				$class = 'notice notice-error';
				/* translators: %s: the path to the plugin */
				$message = sprintf( __( 'The composer.json file was not found within %s. No classes will be loaded.', 'axeptio-sdk-integration' ), XPWP_PATH );

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

	( new Activation_Hook() )->set_plugin_activated();
	// First load the init scripts in case any rewrite functionality is being loaded.
	init();

	// Update the WP Rocket rules on the .htaccess file.
	if ( function_exists( 'flush_rocket_htaccess' ) ) {
		flush_rocket_htaccess();

		// Regenerate the config file.
		rocket_generate_config_file();

		// Clear WP Rocket cache.
		rocket_clean_domain();
	}

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

	return XPWP_URL . "dist/js/{$script}.js";
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

	return XPWP_URL . "dist/css/{$stylesheet}.css";
}


/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {
	$screen = get_current_screen();

	if ( ! in_array( $screen->id, array( 'toplevel_page_axeptio-wordpress-plugin', 'axeptio_page_axeptio-plugin-manager' ), true ) ) {
		return;
	}

	wp_enqueue_media();
	$dependencies = get_asset_info( 'admin', 'dependencies' ) ?? array();
	wp_enqueue_script(
		'axeptio/main',
		script_url( 'backend/app', 'admin' ),
		array_merge( $dependencies, array( 'wp-i18n' ) ),
		get_asset_info( 'admin', 'version' ),
		true
	);
	wp_set_script_translations( 'axeptio/main', 'axeptio-sdk-integration' );

	wp_localize_script(
		'axeptio/main',
		'Axeptio',
		array(
			'errors' => array(
				'non_existing_account_id' => \Axeptio\Plugin\get_template_part(
					'admin/main/fields/validation-error',
					array(
						'title'   => __( "We couldn't find a published project matching this ID.", 'axeptio-sdk-integration' ),
						'message' => __( 'Please check that your project ID is correct and that your project has been published in the Axeptio dashboard. You can find your project ID in your project settings.', 'axeptio-sdk-integration' ),
					),
					false
					),
				'empty_cookies'           => \Axeptio\Plugin\get_template_part(
					'admin/main/fields/validation-error',
					array(
						'title'   => __( "Your project was found, but it doesn't have a cookie banner configured yet.", 'axeptio-sdk-integration' ),
						'message' => __( 'Please create at least one cookie consent configuration in the Axeptio dashboard, then come back here and try again.', 'axeptio-sdk-integration' ),
					),
					false
					),
				'verification_error'      => \Axeptio\Plugin\get_template_part(
					'admin/main/fields/validation-error',
					array(
						'title'   => __( 'Unable to connect to Axeptio.', 'axeptio-sdk-integration' ),
						'message' => __( 'Please check your internet connection and try again. If the problem persists, the Axeptio service may be temporarily unavailable.', 'axeptio-sdk-integration' ),
					),
					false
					),
				'empty_account_id'        => \Axeptio\Plugin\get_template_part(
					'admin/main/fields/validation-error',
					array(
						'title' => __( 'Please enter an account ID', 'axeptio-sdk-integration' ),
					),
					false
					),
			),
		)
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
		style_url( 'backend/main', 'admin' ),
		array(),
		Utility\get_asset_info( 'shared', 'version' ),
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
