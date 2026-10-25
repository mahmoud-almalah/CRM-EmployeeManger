# ADR 0002: Command and query buses

- Status: proposed (accepted once the project owner reviews it)
- Date: 2026-10-25

## Context

Controllers need one entry point into the Application layer that does not leak how the work is done. Reads and writes have different needs: writes run in transactions and may change several records, reads return shapes optimised for the client.

## Decision

Two buses, `CommandBusContract` and `QueryBusContract`, each mapping a message class to a handler class. Commands change state; queries never do. Handlers are plain classes, registered in `ApplicationServiceProvider`, and resolved the first time their message is dispatched.

Messages are immutable objects with typed properties. Handlers check the type of the message they receive and throw `InvalidArgumentException` otherwise, because the abstract `handle()` signature can only name the base class.

The bus is deliberately synchronous and in-process. No queueing, middleware pipeline or event sourcing is attempted.

## Consequences

- A use case is one message and one handler, easy to find and test.
- Transactions and authorization side effects have one obvious home: the handler.
- The `instanceof` guard in each handler is a little repetitive. Making the handlers generic would remove it, but static analysis support for that in PHP is weak.
- Laravel's own bus (`Illuminate\Bus\Dispatcher`) is not used. It is designed around queued jobs, which is not what this application needs yet.

## Notes

The buses were part of the original design. Drafted with Claude Code, which also proposed the Sprint 1 clean-up: lazy handler resolution and a typed `HandlerNotFoundException`, replacing a version that built every handler at boot and dumped the handler map to `error_log`.
