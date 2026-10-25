<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\Contracts\UserRepositoryContract;
use Application\User\Contracts\UserServiceContract;
use Application\User\Data\UserData;
use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class UserService implements UserServiceContract
{
    public function __construct(
        private readonly UserRepositoryContract $repository,
    ) {}

    public function query(): Builder
    {
        return $this->repository->query();
    }

    public function all(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    public function find(string $id, array $with = []): User
    {
        /** @var User */
        return $this->repository->find($id, $with);
    }

    public function create(UserData $data, array $override = []): User
    {
        /** @var User */
        return $this->repository->create($data, $override);
    }

    public function update(string $id, UserData $data, array $override = []): User
    {
        /** @var User */
        return $this->repository->update($id, $data, $override);
    }

    public function delete(string $id): void
    {
        $this->repository->delete($id);
    }
}
