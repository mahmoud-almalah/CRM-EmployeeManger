<?php

declare(strict_types=1);

namespace Application\Bus\Query;

abstract class QueryHandler
{
    abstract public function handle(Query $query): mixed;
}
