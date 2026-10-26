<?php

declare(strict_types=1);

namespace Application\Country\Contracts;

use Application\Country\Data\CountryData;
use Domain\Country\Entities\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface CountryServiceContract
{
    public function query(): Builder;

    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): Country;

    public function create(CountryData $data, array $override = []): Country;

    public function update(string $id, CountryData $data, array $override = []): Country;

    public function delete(string $id): void;

    public function isInUse(string $id): bool;
}
