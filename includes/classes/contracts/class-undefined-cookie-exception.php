<?php
/**
 * Undefined Cookie Exception
 *
 * @package Axeptio
 */

declare(strict_types=1);

namespace Axeptio\Plugin\Contracts;

use Exception;

/**
 * Exception thrown when a required cookie is not defined.
 */
class Undefined_Cookie_Exception extends Exception {
}
