<?php
/**
 * Cookie Saver
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Backend;

use Axeptio\Plugin\Models\Project_Versions;
use Axeptio\Plugin\Module;

class Settings extends Module {
	/**
	 * Module can run within the current context.
	 *
	 * @return true
	 */
	public function can_register(): bool
	{
		return true;
	}

	/**
	 * Registering the cookie actions
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'update_option_axeptio_settings', array( $this, 'historize_version' ), 20, 2 );
	}

	public function historize_version($old_value, $new_value)
	{
		$localized_version = Project_Versions::get_localized_versions();

		$datas = [];
		foreach ($localized_version as $option_key) {
			$datas[$option_key] = $old_value[$option_key];
		}

		$axeptio_versions = get_option('axeptio_versions');
		$axeptio_versions[$old_value['client_id']] = $datas;
		update_option('axeptio_versions', $axeptio_versions);

	}
}
