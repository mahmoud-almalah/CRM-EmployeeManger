<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Data\DataEntity;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements RepositoryInterface
{
    private readonly DatabaseManager $database;

    public function __construct(
        private readonly Model $model,
    ) {
        $this->database = app('db');
    }

    /**
     * A new query on every call, so filters from one call never leak into the next.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(array $with = []): Collection
    {
        return $this->query()->with($with)->get();
    }

    public function find(string $id, array $with = []): ?object
    {
        return $this->query()->with($with)->findOrFail($id);
    }

    public function create(DataEntity $entity, array $override = []): void
    {
        $this->database->transaction(
            callback: fn () => $this->query()->create(
                array_merge($entity->toArray(), $override),
            ),
            attempts: 3,
        );
    }

    public function update(string $id, DataEntity $entity): void
    {
        $this->database->transaction(
            callback: fn () => $this->query()->findOrFail($id)->update($entity->toArray()),
            attempts: 3,
        );
    }

    public function delete(string $id): void
    {
        $this->database->transaction(
            callback: fn () => $this->query()->findOrFail($id)->delete(),
            attempts: 3,
        );
    }
}
