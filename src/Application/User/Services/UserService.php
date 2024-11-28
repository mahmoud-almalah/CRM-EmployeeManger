<?php

namespace Application\User\Services;

use Application\User\Contracts\UserServiceContract;
use Application\User\Data\UserData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Infrastructure\User\Persistence\Repositories\UserRepository;

final class UserService implements UserServiceContract
{
    public function __construct(
        protected UserRepository $repository
    ){
    }

    public function query(): Builder
    {
        return $this->repository->query();
    }

    public function all(array $with = []): Collection
    {
        return $this->repository->all(
            with: $with
        );
    }

    public function find(string $id, array $with = []): null|object
    {
        return $this->repository->find(
            id: $id,
            with: $with
        );
    }

    public function create(UserData $data, array $override = []): void
    {
        $this->repository->create(
            entity: $data,
            override: $override
        );
    }

    public function update(string $id, UserData $data): void
    {
        $this->repository->update(
            id: $id,
            entity: $data
        );
    }

    public function delete(string $id): void
    {
        $this->repository->delete(
            id: $id
        );
    }
}
