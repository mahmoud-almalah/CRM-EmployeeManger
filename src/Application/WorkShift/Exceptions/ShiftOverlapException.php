<?php

declare(strict_types=1);

namespace Application\WorkShift\Exceptions;

use Application\Exceptions\BusinessConflictException;
use Domain\WorkShift\Entities\WorkShift;

final class ShiftOverlapException extends BusinessConflictException
{
    public function __construct(WorkShift $other)
    {
        parent::__construct(sprintf(
            'The shift overlaps the %s shift (%s-%s) of the same work area.',
            $other->name->value,
            substr($other->start_time, 0, 5),
            substr($other->end_time, 0, 5),
        ));
    }
}
