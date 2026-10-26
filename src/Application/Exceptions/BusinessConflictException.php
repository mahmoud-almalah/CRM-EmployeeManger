<?php

declare(strict_types=1);

namespace Application\Exceptions;

use RuntimeException;

/**
 * A request that is valid but clashes with the current state of the data, for
 * example deleting a row that other rows still point to. Rendered as 409.
 */
class BusinessConflictException extends RuntimeException {}
