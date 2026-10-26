<?php

declare(strict_types=1);

namespace Shared\Enums;

use Shared\Traits\EnumValues;

enum ShiftName: string
{
    use EnumValues;

    case Morning = 'morning';
    case Afternoon = 'afternoon';
    case Night = 'night';
}
