<?php
/**
 * Axeptio Cookie Manager
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Cookies;

use Axeptio\Plugin\Contracts\Cookie_Interface;
use Axeptio\Plugin\Contracts\Undefined_Cookie_Exception;
use LogicException;

/**
 * Manages the lifecycle of Axeptio consent cookies.
 */
class Axeptio_Cookie_Manager {

	/**
	 * Cookie instances indexed by class name.
	 *
	 * @var array<string, Cookie_Interface|null>
	 */
	private array $cookie_instances = array(
		Axeptio_Cookies::class           => null,
		Authorized_Vendor_Cookies::class => null,
		All_Vendor_Cookies::class        => null,
	);

	/**
	 * Get all cookie instances.
	 *
	 * @return array<string, Cookie_Interface|null> Cookie instances.
	 */
	public function get_cookies_instances(): array {
		return $this->cookie_instances;
	}

	/**
	 * Add Axeptio cookies instance.
	 *
	 * @param Axeptio_Cookies $axeptio_cookies Axeptio cookies instance.
	 * @return void
	 */
	public function add_axeptio_cookies( Axeptio_Cookies $axeptio_cookies ): void {
		$this->cookie_instances[ Axeptio_Cookies::class ] = $axeptio_cookies;
	}

	/**
	 * Add authorized vendor cookies instance.
	 *
	 * @param Authorized_Vendor_Cookies $authorized_vendor_cookies Authorized vendor cookies instance.
	 * @return void
	 */
	public function add_authorized_vendor_cookies( Authorized_Vendor_Cookies $authorized_vendor_cookies ): void {
		$this->cookie_instances[ Authorized_Vendor_Cookies::class ] = $authorized_vendor_cookies;
	}

	/**
	 * Add all vendor cookies instance.
	 *
	 * @param All_Vendor_Cookies $all_vendor_cookies All vendor cookies instance.
	 * @return void
	 */
	public function add_all_vendor_cookies( All_Vendor_Cookies $all_vendor_cookies ): void {
		$this->cookie_instances[ All_Vendor_Cookies::class ] = $all_vendor_cookies;
	}

	/**
	 * Set all cookies after ensuring they are all defined.
	 *
	 * @throws Undefined_Cookie_Exception If any required cookie is not defined.
	 * @return void
	 */
	public function set(): void {
		$this->ensure_all_cookies_are_defined();
		$this->set_cookies();
	}

	/**
	 * Set all registered cookies.
	 *
	 * @return void
	 */
	public function set_cookies(): void {
		foreach ( $this->cookie_instances as $instance ) {
			if ( $instance instanceof Cookie_Interface ) {
				$instance->set();
			}
		}
	}

	/**
	 * Ensure all required cookies are defined.
	 *
	 * @throws Undefined_Cookie_Exception If a required cookie is not defined.
	 * @throws LogicException If a cookie instance doesn't implement Cookie_Interface.
	 * @return void
	 */
	public function ensure_all_cookies_are_defined(): void {
		foreach ( $this->cookie_instances as $namespace => $instance ) {
			if ( null === $instance ) {
				$class_name_parts = explode( '\\', $namespace );
				$class_name       = array_pop( $class_name_parts );

				throw new Undefined_Cookie_Exception(
					sprintf(
						/* translators: 1: Class name, 2: Method name, 3: Class name, 4: Class name, 5: Builder class name */
						'%1$s has not been set. You must call the add_%2$s method with a %3$s parameter. You can build this param by using the %4$s_Builder',
						esc_html( $class_name ),
						esc_html( $this->camel_to_snake( $class_name ) ),
						esc_html( $class_name ),
						esc_html( $class_name )
					)
				);
			}

			if ( ! $instance instanceof Cookie_Interface ) {
				throw new LogicException(
					sprintf(
						'%s must implement %s',
						esc_html( $namespace ),
						esc_html( Cookie_Interface::class )
					)
				);
			}
		}
	}

	/**
	 * Convert CamelCase to snake_case.
	 *
	 * @param string $input CamelCase string.
	 * @return string snake_case string.
	 */
	private function camel_to_snake( string $input ): string {
		return strtolower( (string) preg_replace( '/(?<!^)[A-Z]/', '_$0', $input ) );
	}
}
