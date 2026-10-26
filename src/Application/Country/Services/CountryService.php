<?php

declare(strict_types=1);

namespace Application\Country\Services;

use Application\Country\Contracts\CountryRepositoryContract;
use Application\Country\Contracts\CountryServiceContract;
use Application\Country\Data\CountryData;
use Domain\Country\Entities\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class CountryService implements CountryServiceContract
{
    public function __construct(
        private readonly CountryRepositoryContract $repository,
    ) {}

    public function query(): Builder
    {
        return $this->repository->query();
    }

    public function all(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    public function find(string $id, array $with = []): Country
    {
        /** @var Country */
        return $this->repository->find($id, $with);
    }

    public function create(CountryData $data, array $override = []): Country
    {
        /** @var Country */
        return $this->repository->create($data, $override);
    }

    public function update(string $id, CountryData $data, array $override = []): Country
    {
        /** @var Country */
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
