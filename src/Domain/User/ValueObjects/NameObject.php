<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class NameObject implements Stringable
{
    public string $first;

    public ?string $middle;

    public ?string $last;

    public function __construct(string $first, ?string $middle = null, ?string $last = null)
    {
        $this->first = trim($first);
        $this->middle = self::blankToNull($middle);
        $this->last = self::blankToNull($last);

        if ($this->first === '') {
            throw new InvalidArgumentException('A name needs at least a first name.');
        }
    }

    /**
     * @param  array{first: string, middle?: string|null, last?: string|null}  $name
     */
    public static function fromArray(array $name): self
    {
        return new self($name['first'], $name['middle'] ?? null, $name['last'] ?? null);
    }

    /**
     * @return array{first: string, middle: string|null, last: string|null}
     */
    public function toArray(): array
    {
        return [
            'first' => $this->first,
            'middle' => $this->middle,
            'last' => $this->last,
        ];
    }

    public function fullName(): string
    {
        return implode(' ', array_filter([$this->first, $this->middle, $this->last]));
    }

    public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }

    public function __toString(): string
    {
        return $this->fullName();
    }

    private static function blankToNull(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === '' ? null : $value;
    }
}
