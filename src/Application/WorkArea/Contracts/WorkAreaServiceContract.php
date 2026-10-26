<?php

declare(strict_types=1);

namespace Application\WorkArea\Contracts;

use Application\WorkArea\Data\WorkAreaData;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface WorkAreaServiceContract
{
    public function query(): Builder;

    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): WorkArea;

    public function create(WorkAreaData $data, array $override = []): WorkArea;

    public function update(string $id, WorkAreaData $data, array $override = []): WorkArea;

    public function delete(string $id): void;

    public function isInUse(string $id): bool;
}
