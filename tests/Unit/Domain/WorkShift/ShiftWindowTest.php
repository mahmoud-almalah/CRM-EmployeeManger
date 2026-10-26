<?php

use Domain\WorkShift\ValueObjects\ShiftWindow;

it('reads minutes from HH:MM and HH:MM:SS', function () {
    $window = ShiftWindow::fromTimes('09:30', '17:00:00');

    expect($window->start)->toBe(570)
        ->and($window->end)->toBe(1020)
        ->and($window->isOvernight())->toBeFalse();
});

it('treats an end before the start as an overnight shift', function () {
    expect(ShiftWindow::fromTimes('22:00', '06:00')->isOvernight())->toBeTrue();
});

it('rejects equal start and end times and malformed times', function (string $start, string $end) {
    ShiftWindow::fromTimes($start, $end);
})->with([
    'same time' => ['09:00', '09:00'],
    'hour out of range' => ['24:00', '08:00'],
    'minutes out of range' => ['09:60', '10:00'],
    'not a time' => ['nine', '10:00'],
])->throws(InvalidArgumentException::class);

it('detects overlaps between shifts', function (array $a, array $b, bool $overlaps) {
    $first = ShiftWindow::fromTimes(...$a);
    $second = ShiftWindow::fromTimes(...$b);

    expect($first->overlaps($second))->toBe($overlaps)
        ->and($second->overlaps($first))->toBe($overlaps);
})->with([
    'identical' => [['09:00', '17:00'], ['09:00', '17:00'], true],
    'partial' => [['09:00', '17:00'], ['16:00', '20:00'], true],
    'contained' => [['06:00', '22:00'], ['09:00', '10:00'], true],
    'back to back' => [['06:00', '14:00'], ['14:00', '22:00'], false],
    'apart' => [['06:00', '10:00'], ['12:00', '14:00'], false],
    'overnight against the evening' => [['22:00', '06:00'], ['20:00', '23:00'], true],
    'overnight against the early morning' => [['22:00', '06:00'], ['05:00', '07:00'], true],
    'overnight against the afternoon' => [['22:00', '06:00'], ['06:00', '22:00'], false],
    'two overnight shifts' => [['22:00', '06:00'], ['23:00', '02:00'], true],
]);
