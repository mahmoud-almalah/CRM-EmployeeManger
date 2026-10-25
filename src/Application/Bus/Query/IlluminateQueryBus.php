<?php

declare(strict_types=1);

namespace Application\Bus\Query;

use Application\Bus\Contracts\QueryBusContract;
use Application\Bus\Trait\BusMappingTrait;
use Application\Bus\Trait\LoggingTrait;

class IlluminateQueryBus implements QueryBusContract
{
    use BusMappingTrait, LoggingTrait;

    public function ask(Query $query): mixed
    {
        $handler = $this->resolveHandler(get_class($query));

        if ($handler instanceof QueryHandler) {
            $this->logExecution('Query', $query);

            return $handler->handle($query);
        }

        throw new \InvalidArgumentException('Handler is not a valid QueryHandler instance.');
    }
}
