<?php

use Application\Health\Contracts\HealthProbeContract;
use Application\Health\Queries\GetHealthQuery;
use Application\Health\QueriesHandlers\GetHealthQueryHandler;

function probe(string $name, bool $works): HealthProbeContract
{
    return new class($name, $works) implements HealthProbeContract
    {
        public function __construct(private string $name, private bool $works) {}

        public function name(): string
        {
            return $this->name;
        }

        public function check(): void
        {
            if (! $this->works) {
                throw new RuntimeException('secret connection string');
            }
        }
    };
}

it('is healthy when every probe passes', function () {
    $report = (new GetHealthQueryHandler(probe('database', true), probe('cache', true)))->handle(new GetHealthQuery);

    expect($report)->toBe(['healthy' => true, 'checks' => ['database' => 'ok', 'cache' => 'ok']]);
});

it('names the failing probe and keeps running the others', function () {
    $report = (new GetHealthQueryHandler(probe('database', false), probe('cache', true)))->handle(new GetHealthQuery);

    expect($report['healthy'])->toBeFalse()
        ->and($report['checks'])->toBe(['database' => 'failed', 'cache' => 'ok']);
});
