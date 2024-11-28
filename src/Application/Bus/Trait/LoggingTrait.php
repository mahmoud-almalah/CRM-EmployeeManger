<?php

namespace Application\Bus\Trait;

use Illuminate\Support\Facades\Log;

trait LoggingTrait
{
    protected function logExecution(string $type, object $instance): void
    {
        $id = method_exists($instance, 'getId') ? $instance->getId() : 'N/A';
        $createdAt = method_exists($instance, 'getCreatedAt')
            ? $instance->getCreatedAt()->format('Y-m-d H:i:s')
            : 'N/A';

        Log::driver('bus')->error(sprintf(
            "%s Execution: %s, ID: %s, CreatedAt: %s",
            $type,
            get_class($instance),
            $id,
            $createdAt
        ));
    }
}
