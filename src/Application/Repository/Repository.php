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

    private readonly Builder $query;

    public function __construct(
        private readonly Model $model,
    ) {
        $this->database = app('db');
        $this->query = $this->model::query();
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function select(array $columns): Repository
    {
        $this->query->select($columns);

        return $this;
    }

    public function all(array $with = []): Collection
    {
        return $this->query->with(
            relations: $with
        )->get();
    }

    public function find(string $id, array $with = []): ?object
    {
        return $this->query->with(
            relations: $with,
        )->findOrFail(
            id: $id,
        );
    }

    public function create(DataEntity $entity, array $override = []): void
    {
        $this->database->transaction(
            callback: fn () => $this->query->create(
                attributes: array_merge(
                    $entity->toArray(),
                    $override,
                ),
            ),
            attempts: 3,
        );
    }

    public function update(string $id, DataEntity $entity): void
    {
        $this->database->transaction(
            callback: fn () => $this->query->where(
                column: 'id',
                operator: '=',
                value: $id,
            )->update(
                values: $entity->toArray(),
            ),
            attempts: 3,
        );
    }

    public function delete(string $id): void
    {
        $this->database->transaction(
            callback: fn () => $this->query->where(
                column: 'id',
                operator: '=',
                value: $id,
            )->delete(),
            attempts: 3,
        );
    }
}
