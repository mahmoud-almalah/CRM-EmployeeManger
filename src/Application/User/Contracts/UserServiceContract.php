<?php

declare(strict_types=1);

namespace Application\User\Contracts;

use Application\User\Data\UserData;
use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceContract
{
    public function query(): Builder;

    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): User;

    public function create(UserData $data, array $override = []): User;

    public function update(string $id, UserData $data, array $override = []): User;

    public function delete(string $id): void;
}
