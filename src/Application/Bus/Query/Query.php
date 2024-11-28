<?php

declare(strict_types=1);

namespace Application\Bus\Query;

abstract class Query
{
    protected array $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
