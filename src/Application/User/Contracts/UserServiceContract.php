<?php

namespace Application\User\Contracts;

use Application\User\Data\UserData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceContract
{
    public function query(): Builder;

    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): ?object;

    public function create(UserData $data, array $override = []): void;

    public function update(string $id, UserData $data): void;

    public function delete(string $id): void;
}
