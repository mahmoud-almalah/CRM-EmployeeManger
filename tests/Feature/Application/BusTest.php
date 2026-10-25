<?php

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Bus\Command\IlluminateCommandBus;
use Application\Bus\Exceptions\HandlerNotFoundException;
use Application\Bus\Query\IlluminateQueryBus;
use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;

final class PingCommand extends Command {}

final class PingHandler extends CommandHandler
{
    public static int $built = 0;

    public function __construct()
    {
        self::$built++;
    }

    public function handle(Command $command): string
    {
        return 'pong';
    }
}

final class EchoQuery extends Query {}

final class EchoHandler extends QueryHandler
{
    public function handle(Query $query): array
    {
        return $query->getParameters();
    }
}

it('dispatches a command to its registered handler', function () {
    $bus = new IlluminateCommandBus;
    $bus->register([PingCommand::class => PingHandler::class]);

    expect($bus->dispatch(new PingCommand))->toBe('pong');
});

it('only builds a handler when a message needs it', function () {
    PingHandler::$built = 0;

    $bus = new IlluminateCommandBus;
    $bus->register([PingCommand::class => PingHandler::class]);

    expect(PingHandler::$built)->toBe(0);

    $bus->dispatch(new PingCommand);
    $bus->dispatch(new PingCommand);

    expect(PingHandler::$built)->toBe(1);
});

it('answers a query through its handler', function () {
    $bus = new IlluminateQueryBus;
    $bus->register([EchoQuery::class => EchoHandler::class]);

    expect($bus->ask(new EchoQuery(['a' => 1])))->toBe(['a' => 1]);
});

it('throws a typed exception when nothing handles a message', function () {
    (new IlluminateCommandBus)->dispatch(new PingCommand);
})->throws(HandlerNotFoundException::class);
