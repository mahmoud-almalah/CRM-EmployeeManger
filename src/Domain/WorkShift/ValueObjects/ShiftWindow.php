<?php

declare(strict_types=1);

namespace Domain\WorkShift\ValueObjects;

use InvalidArgumentException;

/**
 * The part of the day a shift covers, in minutes since midnight. A shift whose
 * end is earlier than its start runs overnight, into the next day.
 */
final class ShiftWindow
{
    private const DAY = 1440;

    public readonly int $start;

    public readonly int $end;

    public function __construct(int $start, int $end)
    {
        if ($start < 0 || $start >= self::DAY || $end < 0 || $end >= self::DAY) {
            throw new InvalidArgumentException('A shift time must be between 00:00 and 23:59.');
        }

        if ($start === $end) {
            throw new InvalidArgumentException('A shift cannot start and end at the same time.');
        }

        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @param  string  $start  "HH:MM" or "HH:MM:SS"
     * @param  string  $end  "HH:MM" or "HH:MM:SS"
     */
    public static function fromTimes(string $start, string $end): self
    {
        return new self(self::minutes($start), self::minutes($end));
    }

    public function isOvernight(): bool
    {
        return $this->end < $this->start;
    }

    /**
     * Two shifts overlap when they share any minute. One that ends exactly when
     * the other starts does not overlap it.
     */
    public function overlaps(self $other): bool
    {
        foreach ($this->segments() as [$aStart, $aEnd]) {
            foreach ($other->segments() as [$bStart, $bEnd]) {
                if ($aStart < $bEnd && $bStart < $aEnd) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<int, array{0: int, 1: int}>
     */
    private function segments(): array
    {
        return $this->isOvernight()
            ? [[$this->start, self::DAY], [0, $this->end]]
            : [[$this->start, $this->end]];
    }

    private static function minutes(string $time): int
    {
        if (! preg_match('/^([01]\d|2[0-3]):([0-5]\d)(?::[0-5]\d)?$/', $time, $parts)) {
            throw new InvalidArgumentException("Invalid time \"$time\", expected HH:MM.");
        }

        return ((int) $parts[1]) * 60 + (int) $parts[2];
    }
}
