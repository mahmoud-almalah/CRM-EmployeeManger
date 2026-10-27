# Operations

What to look at when the API is running somewhere other than a developer laptop.

## Health check

`GET /api/health` needs no token. It checks the database, the cache and the queue connection and answers:

```json
{ "status": "ok", "checks": { "database": "ok", "cache": "ok", "queue": "ok" } }
```

When a dependency is down the status is `503`, `status` is `degraded` and the failed check says `failed`. The response never says why; the cause is written to the log with the check name. Point a load balancer or uptime monitor at it.

`GET /up` is Laravel's own liveness route. It only proves the framework boots, so use it for "is the process alive" and `/api/health` for "can it serve requests".

A new dependency is added by writing a probe in `src/Infrastructure/Health` that implements `HealthProbeContract` and listing it in `InfrastructureServiceProvider`.

## Request ids

Every response carries an `X-Request-Id` header, and every log line written while the request runs has the same `request_id` in its context. When a user reports a problem, ask for that header and search the log for it.

A client may send its own `X-Request-Id` (8 to 64 letters, digits, dots, dashes or underscores) to follow a call across services. Anything else is replaced with a generated id, because the value ends up in logs.

## Logging

Locally the default `stack` channel writes to `storage/logs/laravel.log`. In a container, log JSON to stderr so the platform collects it:

```env
LOG_CHANNEL=stderr
LOG_STDERR_FORMATTER=Monolog\Formatter\JsonFormatter
```

## Not done yet

Error tracking with release versions and an alert when the health check fails need an external service, so they are tracked separately on the board.
