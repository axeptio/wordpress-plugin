<?php
namespace Axeptio\Contracts;

interface Migration_Interface {
	/**
	 * Run the upgrade migration.
	 *
	 * @return void
	 */
	public function up();

	/**
	 * Run the downgrade migration.
	 *
	 * @return void
	 */
	public function down();
}
