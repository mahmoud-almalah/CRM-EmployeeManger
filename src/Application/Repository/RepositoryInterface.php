<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Data\DataEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface RepositoryInterface
{
    public function query(): Builder;

    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): ?object;

    public function create(DataEntity $entity, array $override = []): void;

    public function update(string $id, DataEntity $entity): void;

    public function delete(string $id): void;
}
