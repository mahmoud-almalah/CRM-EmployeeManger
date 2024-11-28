<?php

namespace Application\Bus\Trait;

trait BusMappingTrait
{
    protected array $map = [];

    public function register(array $map): void
    {
        foreach ($map as $key => $handler) {
            $this->map[$key] = is_string($handler) ? app($handler) : $handler;
        }
    }

    public function resolveHandler(string $key): mixed
    {
        if (!array_key_exists($key, $this->map)) {
            error_log("Registered map: " . json_encode(array_keys($this->map), JSON_THROW_ON_ERROR));
            throw new \InvalidArgumentException("No handler registered for key: $key");
        }

        return $this->map[$key];
    }
}
