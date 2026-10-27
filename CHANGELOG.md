# Changelog

All notable changes are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added
- Architecture tests that enforce the dependency rule and the bus conventions (#131).
- The test suite also runs on MySQL 8.4 in CI (#114).
- `GET /api/health` reports the database, cache and queue, an `X-Request-Id` header on every response, and the id on every log line (#117). See `docs/operations.md`.
- `Dockerfile` and `docker-compose.yml`: a development stack with MySQL, a production image without dev dependencies, and an `app:install` command for first-time setup (#113).

### Fixed
- Signing in returned 500 on MySQL: Passport stored `user_id` as a bigint while users have UUID keys. SQLite hid it. A migration converts the columns (#114).

## [0.2.0] - 2026-10-26

Organization structure (Sprint 2): countries, cities, positions, work areas, shifts and supervisors, and the user's links to them.

### Security
- Upgraded to Laravel 12 and Passport 13. `composer audit` reported 59 advisories in 16 packages; every Laravel 11.x release up to 11.57 was affected by at least one, so the fix is the new major version. `composer audit` is now clean.
- Added `SECURITY.md`, Dependabot updates for composer and GitHub Actions, and a dependency audit job in CI that also runs weekly.

### Added
- MIT LICENSE file
- `ApiResource` base class for API resources and automatic discovery of `src/Presentation/*/routes/api.php`
- Countries: `GET/POST /api/countries`, `GET/PUT/DELETE /api/countries/{id}` with filters, sorting and a 409 when the country is still in use; `CountryFactory`
- Cities: `/api/cities` CRUD with `filter[country_id]`, `include=country`, validated coordinates (lat/lng) and timezone; `CityFactory`
- Positions: `/api/positions` CRUD with a unique name, `filter[name]` and a 409 when users still hold the position; `PositionFactory`
- Work areas: `/api/work-areas` CRUD with an active manager, a `WorkAreaStatus` enum (open, closed, under_maintenance), `filter[city_id|country_id|status]`, `include=city,country,manager`; `WorkAreaFactory`
- Work shifts: `/api/work-areas/{id}/shifts` CRUD with `HH:MM` times, overnight shifts, a 409 when a shift overlaps another one of the same work area or still has employees; `WorkShiftFactory`, `include=shifts` on work areas
- Test helper `organization()` is built entirely from factories
- Supervisors: `/api/supervisors` CRUD (uuid ids, hashed password, `SupervisorStatus` enum, unique email and phone, filters by name, email and status); `SupervisorFactory`
- `supervisors.status` is now a string column (new migration), matching the users table
- Users: `country`, `city`, `position`, `workArea` and `workShift` relations; `include=country,city,position,workArea,workShift` on `GET /api/users` and `GET /api/users/{id}`; the user resource returns the five foreign keys
- Creating or updating a user checks that the city belongs to the country and the shift belongs to the work area

### Changed
- CI workflow runs with read-only permissions.
- `BusinessConflictException` is rendered as 409 with the usual error body.
- `UserResource` extends `ApiResource` and also returns the five organization foreign keys.

### Fixed
- `work_areas.city` and `work_areas.country` are now `city_id` and `country_id` (new migration, the original is untouched). Verified with `migrate:fresh`, rollback and migrate on SQLite; not run on MySQL.

## [0.1.0] - 2026-10-25

First release: the foundation and the User module (Sprint 1).

### Added
- Passport authentication: `POST /api/auth/login`, `POST /api/auth/logout`, `GET /api/auth/me`
- Roles (`admin`, `hr`, `manager`, `employee`) and a `resource.action` permission matrix, seeded from one place
- User module: create, update, soft delete, list with filters, sorting and pagination, show with `include=roles`
- Command and query buses with lazy handler resolution
- Consistent JSON error responses for the API
- `UserFactory` with states and a development seeder
- GitHub issue and pull request templates, CODEOWNERS and a CI pipeline (pint, phpstan, pest)
- Architecture guide and ADRs 0001 to 0003

### Changed
- Value objects (name, email, phone) moved from Application to Domain; email and phone are stored as plain strings so unique indexes and lookups work
- Repositories build a fresh query per call and return the model from `create` and `update`
- `users.status` is a string matching `UserStatus`; the integer `role` column was removed in favour of spatie roles
- `UserService` depends on `UserRepositoryContract` instead of the infrastructure class

### Fixed
- `scopeInactive` filtered on a column that does not exist; `scopeNotTerminated` leaked an ungrouped `OR` into chained queries
- A fresh clone could not run `composer install` because `storage/framework` was ignored as a whole
- The auth provider pointed at a model that does not exist

[0.1.0]: https://github.com/mahmoud-almalah/employee-manager-api/releases/tag/v0.1.0
