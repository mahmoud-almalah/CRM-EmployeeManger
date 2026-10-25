<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Data\DataEntity;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property-read DatabaseManager $database
 * @property-read Builder $query
 */
interface RepositoryInterface
{
    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): ?object;

    public function create(DataEntity $entity, array $override = []): void;

    public function update(string $id, DataEntity $entity): void;

    public function delete(string $id): void;
}
