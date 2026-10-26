<?php

declare(strict_types=1);

namespace Shared\Enums;

use Shared\Traits\EnumValues;

enum WorkAreaStatus: string
{
    use EnumValues;

    case Open = 'open';
    case Closed = 'closed';
    case UnderMaintenance = 'under_maintenance';
}
