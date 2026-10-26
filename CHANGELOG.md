# Changelog

All notable changes are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Security
- Upgraded to Laravel 12 and Passport 13. `composer audit` reported 59 advisories in 16 packages; every Laravel 11.x release up to 11.57 was affected by at least one, so the fix is the new major version. `composer audit` is now clean.
- Added `SECURITY.md`, Dependabot updates for composer and GitHub Actions, and a dependency audit job in CI that also runs weekly.

### Added
- MIT LICENSE file
- `ApiResource` base class for API resources and automatic discovery of `src/Presentation/*/routes/api.php`
- Countries: `GET/POST /api/countries`, `GET/PUT/DELETE /api/countries/{id}` with filters, sorting and a 409 when the country is still in use; `CountryFactory`

### Changed
- CI workflow runs with read-only permissions.

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
