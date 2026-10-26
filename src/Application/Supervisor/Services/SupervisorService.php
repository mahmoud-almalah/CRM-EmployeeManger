<?php

declare(strict_types=1);

namespace Application\Supervisor\Services;

use Application\Supervisor\Contracts\SupervisorRepositoryContract;
use Application\Supervisor\Contracts\SupervisorServiceContract;
use Application\Supervisor\Data\SupervisorData;
use Domain\Supervisor\Entities\Supervisor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class SupervisorService implements SupervisorServiceContract
{
    public function __construct(
        private readonly SupervisorRepositoryContract $repository,
    ) {}

    public function query(): Builder
    {
        return $this->repository->query();
    }

    public function all(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    public function find(string $id, array $with = []): Supervisor
    {
        /** @var Supervisor */
        return $this->repository->find($id, $with);
    }

    public function create(SupervisorData $data, array $override = []): Supervisor
    {
        /** @var Supervisor */
        return $this->repository->create($data, $override);
    }

    public function update(string $id, SupervisorData $data, array $override = []): Supervisor
    {
        /** @var Supervisor */
        return $this->repository->update($id, $data, $override);
    }

    public function delete(string $id): void
    {
        $this->repository->delete($id);
    }
}
