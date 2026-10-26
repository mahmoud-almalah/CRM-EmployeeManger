<?php

declare(strict_types=1);

namespace Application\City\Services;

use Application\City\Contracts\CityRepositoryContract;
use Application\City\Contracts\CityServiceContract;
use Application\City\Data\CityData;
use Domain\City\Entities\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class CityService implements CityServiceContract
{
    public function __construct(
        private readonly CityRepositoryContract $repository,
    ) {}

    public function query(): Builder
    {
        return $this->repository->query();
    }

    public function all(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    public function find(string $id, array $with = []): City
    {
        /** @var City */
        return $this->repository->find($id, $with);
    }

    public function create(CityData $data, array $override = []): City
    {
        /** @var City */
        return $this->repository->create($data, $override);
    }

    public function update(string $id, CityData $data, array $override = []): City
    {
        /** @var City */
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
