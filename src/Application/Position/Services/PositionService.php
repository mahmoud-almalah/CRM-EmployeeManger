<?php

declare(strict_types=1);

namespace Application\Position\Services;

use Application\Position\Contracts\PositionRepositoryContract;
use Application\Position\Contracts\PositionServiceContract;
use Application\Position\Data\PositionData;
use Domain\Position\Entities\Position;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class PositionService implements PositionServiceContract
{
    public function __construct(
        private readonly PositionRepositoryContract $repository,
    ) {}

    public function query(): Builder
    {
        return $this->repository->query();
    }

    public function all(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    public function find(string $id, array $with = []): Position
    {
        /** @var Position */
        return $this->repository->find($id, $with);
    }

    public function create(PositionData $data, array $override = []): Position
    {
        /** @var Position */
        return $this->repository->create($data, $override);
    }

    public function update(string $id, PositionData $data, array $override = []): Position
    {
        /** @var Position */
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
