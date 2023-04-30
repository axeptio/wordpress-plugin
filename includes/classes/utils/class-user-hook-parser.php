<?php
/**
 * User Hook Parser.
 *
 * @package Axeptio
 */

namespace Axeptio\Utils;

/**
 * Class User_Hook_Parser.
 */
class User_Hook_Parser {
	/**
	 * Hooks array.
	 *
	 * @var array
	 */
	private $hooks = array();

	/**
	 * Constructor.
	 *
	 * @param string $hook_instructions Hook instructions.
	 */
	public function __construct( $hook_instructions ) { //phpcs:ignore
		$this->parse_instructions( $hook_instructions );
	}

	/**
	 * Get hooks array.
	 *
	 * @return array
	 */
	public function get_hooks() {
		return $this->hooks;
	}

	/**
	 * Parse hook instructions.
	 *
	 * @param string $hook_instructions Hook instructions.
	 */
	private function parse_instructions( $hook_instructions ) {
		$lines = explode( "\n", $hook_instructions );

		foreach ( $lines as $line ) {
			$line = trim( $line );

			if ( ! empty( $line ) ) {
				list( $hook_info, $callback, $class, $priority ) = $this->parse_line( $line );

				$this->hooks[] = array(
					'hook'     => $hook_info,
					'class'    => $class,
					'callback' => $callback,
					'priority' => $priority,
				);
			}
		}
	}

	/**
	 * Parse single hook line.
	 *
	 * @param string $line Hook line.
	 *
	 * @return array
	 */
	private function parse_line( $line ) {
		$parts    = array_map( 'trim', explode( '>', $line, 2 ) );
		$callback = $parts[1];

		if ( preg_match( '/(.*)\s*\((\d+)\)$/', $callback, $matches ) ) {
			$callback = trim( $matches[1] );
			$priority = intval( $matches[2] );
		} else {
			$priority = null;
		}

		if ( preg_match( '/^\[([^:]+):class,([^]]+)\]$/', $callback, $class_matches ) ) {
			$callback = trim( $class_matches[2] );
			$class    = trim( $class_matches[1] );
		} else {
			$class = null;
		}

		return array(
			$parts[0],
			$callback,
			$class,
			$priority,
		);
	}
}
