<?php

declare(strict_types=1);

namespace Application\User\QueriesHandlers;

/**
 * The relations a client may ask for with ?include=, by relation name.
 */
final class UserIncludes
{
    public const ALLOWED = ['roles', 'country', 'city', 'position', 'workArea', 'workShift'];
}
