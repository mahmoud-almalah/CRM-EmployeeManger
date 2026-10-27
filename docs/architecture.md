# Architecture

The code under `src/` is split into four layers plus a small shared kernel. Laravel itself (`app/`, `bootstrap/`, `config/`) only wires things together.

```
src/
├── Domain/          Entities, value objects, casts, scopes. No knowledge of HTTP or use cases.
├── Application/     Use cases: commands, queries, handlers, services, contracts, DTOs.
├── Infrastructure/  Implementations of Application contracts (repositories, providers).
├── Presentation/    HTTP: controllers, form requests, resources, routes, error rendering.
└── Shared/          Enums and traits used by more than one layer.
```

## The dependency rule

Dependencies point inward.

```mermaid
flowchart LR
    P[Presentation] --> A[Application]
    A --> D[Domain]
    I[Infrastructure] --> A
    I --> D
    P -.-> D
    A --> S[Shared]
    D --> S
```

- **Domain** depends on nothing except `Shared` (and the Eloquent base classes the entities extend).
- **Application** depends on Domain. It defines contracts (for example `UserRepositoryContract`) and never imports a class from Infrastructure.
- **Infrastructure** implements those contracts. The binding is declared in `InfrastructureServiceProvider`.
- **Presentation** talks to Application through the two buses. Controllers never run queries.

The rule is enforced by `tests/Arch/ArchitectureTest.php`, which runs with the rest of the suite. It fails the build when Domain imports an outer layer, Application imports Infrastructure or Presentation, Presentation imports Infrastructure, or a controller touches the database. It also checks that every module's commands, queries and handlers extend the bus base classes. Modules are found on disk, so a new one is covered without editing the test.

Two deviations are known and recorded rather than hidden: entities reference their Eloquent factories (`Database\Factories`), and form requests run uniqueness lookups with `DB::table()`.

## Request flow

A write goes through the command bus, a read through the query bus.

```mermaid
sequenceDiagram
    participant C as Client
    participant R as FormRequest
    participant K as Controller
    participant B as CommandBus
    participant H as Handler
    participant S as Service
    participant P as Repository
    participant E as Entity (Eloquent)

    C->>R: POST /api/users
    R->>R: authorize (permission middleware) and validate
    R->>K: validated input
    K->>B: dispatch(CreateUserCommand)
    B->>H: resolve handler, log execution
    H->>S: create(UserData)
    S->>P: create(entity)
    P->>E: Model::create (casts turn value objects into columns)
    E-->>K: User
    K-->>C: 201 UserResource
```

The pieces for one operation:

| Piece | Lives in | Example |
| --- | --- | --- |
| Form request | Presentation | `CreateUserRequest` validates and builds `UserData` |
| Command or query | Application | `CreateUserCommand`, `GetUsersQuery` |
| Handler | Application | `CreateUserCommandHandler` (one transaction) |
| Service / contract | Application | `UserService` behind `UserServiceContract` |
| Repository contract | Application | `UserRepositoryContract` |
| Repository | Infrastructure | `UserRepository` over the base `Repository` |
| Entity, value objects | Domain | `User`, `NameObject`, `EmailObject`, `PhoneObject` |
| Resource | Presentation | `UserResource` |

## Buses

`IlluminateCommandBus` and `IlluminateQueryBus` map a message class to a handler class. Handlers are registered in `ApplicationServiceProvider` and built the first time their message is dispatched. A message with no handler throws `HandlerNotFoundException`. Every execution is logged on the `bus` channel at info level. See [ADR 0002](adr/0002-command-query-bus.md).

## Value objects and casts

`Name`, `Email` and `Phone` are value objects that validate on construction. Eloquent casts convert them to and from columns, so the rest of the code never handles raw strings. See [ADR 0003](adr/0003-value-objects-as-casts.md).

| Value object | Column format |
| --- | --- |
| `NameObject` | JSON `{"first","middle","last"}` |
| `EmailObject` | lowercase address |
| `PhoneObject` | `+20 1012345678` (country code, space, number) |

## Errors

`ApiExceptionRenderer` gives every `/api/*` error the same body, `{"message": "...", "errors": {...}}`: 422 validation, 404 not found, 401 unauthenticated or bad credentials, 403 forbidden or inactive account, 409 business conflict, 500 unexpected (cause hidden unless `APP_DEBUG`).

## Authentication and authorization

- Passport issues personal access tokens on the `api` guard (`POST /api/auth/login`).
- Roles (`admin`, `hr`, `manager`, `employee`) and `resource.action` permissions are seeded by `RolesAndPermissionsSeeder`, which is the only place the matrix is defined.
- Routes are protected with the `permission:<name>,api` middleware.

## Testing

- `tests/Unit`: plain PHP, no framework. Value objects, DTOs.
- `tests/Arch`: architecture rules (Pest `arch()`), no database.
- `tests/Feature`: boots the app on in-memory SQLite with `RefreshDatabase`. HTTP endpoints, casts, repositories, buses, permissions.
- CI runs `pint --test`, `phpstan` (larastan, level 5) and `pest` on every pull request.

## Adding a module

Every module follows the same slice. Use `User` as the reference.

1. **Domain**: `src/Domain/<Module>/Entities/<Entity>.php`, plus `Traits/` for scopes and relations, `Casts/` or `ValueObjects/` if needed. Add `@property` docs for typed attributes.
2. **Application**: `Data/` (DTO with `fromArray` and `toArray`), `Contracts/` (service and repository), `Services/`, `Commands/`, `CommandHandlers/`, `Queries/`, `QueriesHandlers/`.
3. **Infrastructure**: `Persistence/Repositories/<Entity>Repository.php` extending `Repository`, bound to its contract in `InfrastructureServiceProvider`.
4. **Presentation**: `Controllers/`, `Requests/`, `Resources/` (extend `Presentation\Resources\ApiResource`), `routes/api.php`. Route files are discovered, so nothing needs registering: `bootstrap/app.php` loads every `src/Presentation/*/routes/api.php`.
5. Register each command and query handler in `ApplicationServiceProvider`, and the service contract in its `$bindings`.
6. Permissions: `RolesAndPermissionsSeeder` already holds `<resource>.view|create|update|delete` for every planned resource; add to the matrix only when a module needs something new.
7. Add a factory in `database/factories` (and a `newFactory()` on the entity, because entities live outside `App\Models`) and feature tests in `tests/Feature/<Module>`. Tests build reference rows with factories, never raw inserts.

### Conventions the organization modules settled on

- **Presentation modules group related entities.** `src/Presentation/Organization` holds the controllers, requests and resources of countries, cities, positions, work areas, shifts and supervisors under one `routes/api.php`. The Application, Domain and Infrastructure layers still have one folder per entity.
- **One request class for create and update.** `CountryRequest` and friends serve POST and PUT; the uniqueness rule ignores `route('id')`, and a missing record is a 404 before validation runs.
- **A delete that would orphan data is a 409.** The handler asks the service whether the row is in use and throws a subclass of `BusinessConflictException` (`CountryInUseException`, `ShiftOverlapException`, ...); `ApiExceptionRenderer` turns it into 409.
- **Business rules that need no database live in the Domain.** `ShiftWindow` (minutes since midnight, overnight aware) decides whether two shifts overlap and is unit tested without booting the framework; the handler only loads the sibling shifts.
- **Nested resources are scoped in the repository.** Shifts are always fetched through their work area (`findInWorkArea`), so a shift id under the wrong area is a 404.
- **Includes are relation names.** `GET /api/users?include=workArea,workShift` uses Spatie's allowlist; an include that is not listed answers 400.

## Shared presentation base

- **Route discovery**: `bootstrap/app.php` globs `src/Presentation/*/routes/api.php`, so a new module only has to add its folder. Every route is served under `/api`.
- **`ApiResource`**: the base class for all resources. It fixes the envelope (`{"data": ...}` for one item, `data`, `links` and `meta` for a paginated list) and provides `iso()` and `timestamps()` so dates look the same in every module.
