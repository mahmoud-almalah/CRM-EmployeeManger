<?php

declare(strict_types=1);

namespace Application\Bus\Command;

use Ramsey\Uuid\Uuid;

abstract class Command
{
    protected string $id;
    protected \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
