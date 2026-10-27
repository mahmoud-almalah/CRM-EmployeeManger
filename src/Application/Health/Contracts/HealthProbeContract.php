<?php

declare(strict_types=1);

namespace Application\Health\Contracts;

/**
 * One dependency the application needs in order to serve traffic.
 */
interface HealthProbeContract
{
    public function name(): string;

    /**
     * Returns normally when the dependency works and throws when it does not.
     */
    public function check(): void;
}
