<?php

declare(strict_types=1);

namespace Application\City\Contracts;

use Application\City\Data\CityData;
use Domain\City\Entities\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface CityServiceContract
{
    public function query(): Builder;

    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): City;

    public function create(CityData $data, array $override = []): City;

    public function update(string $id, CityData $data, array $override = []): City;

    public function delete(string $id): void;

    public function isInUse(string $id): bool;
}
