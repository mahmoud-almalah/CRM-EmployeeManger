<?php

declare(strict_types=1);

namespace Application\Data;

abstract class DataEntity
{
    abstract public function toArray(): array;
}
