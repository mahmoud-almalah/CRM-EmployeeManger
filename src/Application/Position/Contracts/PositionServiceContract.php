<?php

declare(strict_types=1);

namespace Application\Position\Contracts;

use Application\Position\Data\PositionData;
use Domain\Position\Entities\Position;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface PositionServiceContract
{
    public function query(): Builder;

    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): Position;

    public function create(PositionData $data, array $override = []): Position;

    public function update(string $id, PositionData $data, array $override = []): Position;

    public function delete(string $id): void;

    public function isInUse(string $id): bool;
}
