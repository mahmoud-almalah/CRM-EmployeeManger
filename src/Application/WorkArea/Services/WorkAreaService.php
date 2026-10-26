<?php

declare(strict_types=1);

namespace Application\WorkArea\Services;

use Application\WorkArea\Contracts\WorkAreaRepositoryContract;
use Application\WorkArea\Contracts\WorkAreaServiceContract;
use Application\WorkArea\Data\WorkAreaData;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class WorkAreaService implements WorkAreaServiceContract
{
    public function __construct(
        private readonly WorkAreaRepositoryContract $repository,
    ) {}

    public function query(): Builder
    {
        return $this->repository->query();
    }

    public function all(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    public function find(string $id, array $with = []): WorkArea
    {
        /** @var WorkArea */
        return $this->repository->find($id, $with);
    }

    public function create(WorkAreaData $data, array $override = []): WorkArea
    {
        /** @var WorkArea */
        return $this->repository->create($data, $override);
    }

    public function update(string $id, WorkAreaData $data, array $override = []): WorkArea
    {
        /** @var WorkArea */
        return $this->repository->update($id, $data, $override);
    }

    public function delete(string $id): void
    {
        $this->repository->delete($id);
    }

    public function isInUse(string $id): bool
    {
        return $this->repository->isInUse($id);
    }
}
