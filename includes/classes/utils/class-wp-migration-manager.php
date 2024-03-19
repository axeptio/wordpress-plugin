<?php
/**
 * WP Migration Manager
 *
 * Allows you to manage upgrades and downgrades of versions of the extension.
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Utils;

use Axeptio\Plugin\Contracts\Migration_Interface;

class WP_Migration_Manager {
	/**
	 * Path to the migrations folder.
	 *
	 * @var string
	 */
	private $migration_path;

	/**
	 * Plugin version in the database.
	 *
	 * @var string
	 */
	private $current_version;

	/**
	 * Initializes the migration class.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->migration_path  = XPWP_INC . 'classes' . DS . 'migrations';
		$this->current_version = get_option( 'axeptio/version', '0.0.0' );
	}

	/**
	 * Run the migrations (upgrade or downgrade).
	 *
	 * @return void
	 */
	public function migrate() {

		if ( version_compare( XPWP_VERSION, $this->current_version, '>' ) ) {
			$migrations = $this->get_migrations( $this->current_version );
			foreach ( $migrations as $migration ) {
				$this->run_upgrade( $migration );
			}

			$this->update_version();
		} elseif ( version_compare( XPWP_VERSION, $this->current_version, '<' ) ) {
			$migrations = $this->get_migrations( '0.0.0' );

			foreach ( array_reverse( $migrations, true ) as $version => $migration ) {
				if ( version_compare( $version, $this->current_version, '<=' )
					&& version_compare( $version, XPWP_VERSION, '>' ) ) {
					$this->run_downgrade( $migration );
				}
			}

			$this->update_version();
		}
	}

	/**
	 * Update the plugin version in the database.
	 *
	 * @return void
	 */
	private function update_version() {
		update_option( 'axeptio/version', XPWP_VERSION );
	}

	/**
	 * Retrieve the past or future migrations.
	 *
	 * @param string $current_version Current migration version.
	 * @return array
	 */
	private function get_migrations( string $current_version ): array {

		$migrations = array();
		$files      = glob( $this->migration_path . '/class-migration-*.php' );

		foreach ( $files as $file ) {
			$version = str_replace( 'class-migration-', '', basename( $file, '.php' ) );
			if ( version_compare( $version, $current_version, '>' ) ) {
				require_once $file;
				$class_name             = '\\Axeptio\\Plugin\\Migrations\\Migration_' . str_replace( '.', '_', $version );
				$migrations[ $version ] = new $class_name();
			}
		}

		uksort( $migrations, 'version_compare' );

		return $migrations;
	}

	/**
	 * Run the downgrade migration.
	 *
	 * @param Migration_Interface $migration Migration instance.
	 * @return void
	 */
	private function run_downgrade( Migration_Interface $migration ) {
		$migration->down();
	}

	/**
	 * Run the upgrade migration.
	 *
	 * @param Migration_Interface $migration Migration instance.
	 * @return void
	 */
	private function run_upgrade( Migration_Interface $migration ) {
		$migration->up();
	}
}
