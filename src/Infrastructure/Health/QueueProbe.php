<?php

declare(strict_types=1);

namespace Infrastructure\Health;

use Application\Health\Contracts\HealthProbeContract;
use Illuminate\Queue\QueueManager;

final class QueueProbe implements HealthProbeContract
{
    public function __construct(private readonly QueueManager $queue) {}

    public function name(): string
    {
        return 'queue';
    }

    public function check(): void
    {
        // Asking for the size proves the connection works without pushing a job.
        $this->queue->connection()->size();
    }
}
