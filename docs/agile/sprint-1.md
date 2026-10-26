# Sprint 1 - Foundation

- Planned window: 2026-10-25 to 2026-11-01 (one-week iteration on the board)
- Release: [v0.1.0](https://github.com/mahmoud-almalah/employee-manager-api/releases/tag/v0.1.0)
- Board: [project](https://github.com/users/mahmoud-almalah/projects/5), milestone "Sprint 1 - Foundation"

## Goal

Make the codebase safe to build on, then deliver one complete module as the template for the rest: repo governance and CI, the fixes the architecture needed, error handling, authentication, roles, and the whole User lifecycle.

## Scope and outcome

| # | Task | Points |
| --- | --- | --- |
| 2 | Repo scaffolding: templates, CI, storage skeleton | 3 |
| 5 | Fresh query per repository call | 3 |
| 6 | Fix User scopes | 2 |
| 7 | Value objects, casts and UserData | 5 |
| 8 | Correct the users table | 2 |
| 9 | Test setup (SQLite, RefreshDatabase) | 3 |
| 10 | Bus clean-up | 2 |
| 11 | API error handling | 3 |
| 12 | Passport authentication | 5 |
| 13 | Roles and permissions | 3 |
| 14 | User factory and seeder | 2 |
| 15 | Create user | 5 |
| 16 | Update user | 3 |
| 17 | Delete user | 2 |
| 18 | List users | 5 |
| 19 | Show user | 2 |
| 20 | Documentation | 3 |

Committed 53 points, delivered 53. Two epics (#1, #4) track the groups. The test suite grew from 2 placeholder tests to 98 tests.

### Scope changes during the sprint
- **#19** was narrowed from "show user with relations" to "show user with roles". The country, city, position, work area and shift entities do not exist until Sprint 2, so their relations cannot be defined yet. The org relations move to the Sprint 2 task "Wire organization relations into the User entity".
- Unplanned but necessary, done inside existing tasks: committing the `storage/` skeleton (found when `composer install` failed on a fresh clone, in #2), and pointing `config/auth.php` at the real `User` model (found in #14 and #12).
- The permission matrix was corrected in #17: HR had `users.delete`, but the task reserves deletion for admins.

## Review

What can be demonstrated:
- Sign in as `admin`, call `GET /api/users?filter[status]=active&sort=-salary&include=roles`, create and edit users, delete one and watch their token stop working.
- Every endpoint answers the same error shape, and role-based access is enforced by permission.
- CI runs lint, static analysis and tests on every PR; all 17 PRs merged green.

Not in this sprint: the organization tables have no API yet, so users reference rows that tests insert directly.

## Retrospective

**Went well**
- Fixing the structural problems first (repository state, dependency direction, storage formats) meant the User module was written once, on a clean base.
- Writing tests alongside every change caught real defects: a PUT for an unknown id answered 422 instead of 404, unknown filters were silently dropped instead of rejected, and the permission matrix disagreed with the task.
- Splitting reads and writes across two buses made each endpoint small.

**Did not go well**
- Several steps needed a second pass for avoidable reasons: a command property named `$id` clashed with the base class, a handler's `void` return type did not match the abstract signature, and a scope test written as a unit test needed the framework booted. Static analysis and tests caught them, but checking the abstract classes first would have saved time.
- Employees hold `users.view`, so they can list every colleague. That is acceptable for now because salary and personal data are not in the resource, but it is not what the final system should do.

**Actions**
- Sprint 2: add proper factories for the organization tables and replace the `organization()` test helper.
- Sprint 3: the authorization policies task must cover "employees see only themselves" for personal data.
- Raise PHPStan above level 5 once the module count grows.
- This sprint was planned as a week and executed in a single working session, so 53 points is not a baseline for a normal week. Re-estimate velocity from Sprint 2.

## Attribution

Implemented with Claude Code as a pair programmer, under the owner's architecture. Ideas that came from Claude (not from the original design) are noted in the [ADRs](../adr).
