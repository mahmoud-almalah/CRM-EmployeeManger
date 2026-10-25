<?php

declare(strict_types=1);

namespace Shared\Enums;

use Shared\Traits\EnumValues;

enum RoleName: string
{
    use EnumValues;

    case Admin = 'admin';
    case Hr = 'hr';
    case Manager = 'manager';
    case Employee = 'employee';
}
