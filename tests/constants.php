<?php
/**
 * Constants required before the Composer autoloader runs.
 *
 * The autoloader pulls in the plugin's `files` entries, which all start with
 * the `defined( 'ABSPATH' ) || exit;` direct-access guard. Loaded through the
 * PHPUnit bootstrap these constants would come too late — the process exits
 * while requiring the autoloader — so this file is prepended instead, via the
 * `auto_prepend_file` directive set in the `test` Composer script.
 *
 * @package Axeptio
 */

defined( 'ABSPATH' ) || define( 'ABSPATH', __DIR__ . '/' );
defined( 'DS' ) || define( 'DS', DIRECTORY_SEPARATOR );
defined( 'XPWP_PATH' ) || define( 'XPWP_PATH', dirname( __DIR__ ) . '/' );
defined( 'XPWP_INC' ) || define( 'XPWP_INC', XPWP_PATH . 'includes/' );
