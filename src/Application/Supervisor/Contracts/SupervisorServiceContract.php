<?php

declare(strict_types=1);

namespace Application\Supervisor\Contracts;

use Application\Supervisor\Data\SupervisorData;
use Domain\Supervisor\Entities\Supervisor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface SupervisorServiceContract
{
    public function query(): Builder;

    public function all(array $with = []): Collection;

    public function find(string $id, array $with = []): Supervisor;

    public function create(SupervisorData $data, array $override = []): Supervisor;

    public function update(string $id, SupervisorData $data, array $override = []): Supervisor;

    public function delete(string $id): void;
}
