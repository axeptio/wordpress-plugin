<?php
namespace Axeptio\Migrations;

use Axeptio\Models\Settings;

class Migration_1_2_0 implements \Axeptio\Contracts\Migration_Interface {

	/**
	 * Run the upgrade migration.
	 *
	 * @return void
	 */
	public function up() {
		$settings = $this->get_settings();

		update_option( 'axeptio_settings', $settings );

		foreach ( $settings as $key => $value ) {
			delete_option( "xpwp_{$key}" );
		}
	}

	/**
	 * Run the downgrade migration.
	 *
	 * @return void
	 */
	public function down() {    }

	/**
	 * Retrieve needed settings.
	 *
	 * @return array Required settings to migrate.
	 */
	protected function get_settings(): array {
		return array(
			'client_id'  => Settings::get_option( 'xpwp_client_id', '', false ),
			'version'    => Settings::get_option( 'xpwp_version', false, false ),
			'sdk_active' => Settings::get_option( 'xpwp_sdk_active', '0', false ),
		);
	}
}
