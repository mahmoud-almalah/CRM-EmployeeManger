<?php

declare(strict_types=1);

namespace Infrastructure\Health;

use Application\Health\Contracts\HealthProbeContract;
use Illuminate\Database\DatabaseManager;

final class DatabaseProbe implements HealthProbeContract
{
    public function __construct(private readonly DatabaseManager $database) {}

    public function name(): string
    {
        return 'database';
    }

    public function check(): void
    {
        $this->database->connection()->select('select 1');
    }
}
