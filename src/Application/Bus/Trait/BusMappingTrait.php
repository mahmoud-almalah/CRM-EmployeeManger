<?php

declare(strict_types=1);

namespace Application\Bus\Trait;

use Application\Bus\Exceptions\HandlerNotFoundException;

trait BusMappingTrait
{
    /**
     * Message class => handler class or instance. Handlers given as class
     * names are only built when a message for them is dispatched.
     *
     * @var array<class-string, object|string>
     */
    protected array $map = [];

    /**
     * @param  array<class-string, object|string>  $map
     */
    public function register(array $map): void
    {
        foreach ($map as $message => $handler) {
            $this->map[$message] = $handler;
        }
    }

    public function resolveHandler(string $message): object
    {
        if (! array_key_exists($message, $this->map)) {
            throw HandlerNotFoundException::for($message);
        }

        $handler = $this->map[$message];

        if (is_string($handler)) {
            $handler = $this->map[$message] = app($handler);
        }

        return $handler;
    }
}
