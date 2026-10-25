<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command\Command;
use Application\User\Data\UserData;
use Shared\Enums\RoleName;

final class UpdateUserCommand extends Command
{
    public function __construct(
        public readonly string $userId,
        public readonly UserData $data,
        public readonly ?RoleName $role = null,
    ) {
        parent::__construct();
    }
}
