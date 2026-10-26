# Sprint 2 - Organization

- Planned window: iteration "Sprint 2" on the board
- Actual work: one session on 2026-10-26, from 12:47 to about 13:25 (UTC+3), about 40 minutes of wall-clock time including waiting for CI
- Release: [v0.2.0](https://github.com/mahmoud-almalah/employee-manager-api/releases/tag/v0.2.0)
- Board: [project](https://github.com/users/mahmoud-almalah/projects/5), milestone "Sprint 2 - Organization"

## Goal

Everything an employee record points to can be managed through the API: countries, cities, positions, work areas, shifts and supervisors, and the user endpoints can return that data.

## Scope and outcome

| # | Task | Points | PR |
| --- | --- | --- | --- |
| 29 | Shared base for module slices (route discovery, `ApiResource`) | 2 | [#100](https://github.com/mahmoud-almalah/employee-manager-api/pull/100) |
| 22 | Countries module | 3 | [#101](https://github.com/mahmoud-almalah/employee-manager-api/pull/101) |
| 23 | Cities module | 3 | [#102](https://github.com/mahmoud-almalah/employee-manager-api/pull/102) |
| 24 | Positions module | 2 | [#103](https://github.com/mahmoud-almalah/employee-manager-api/pull/103) |
| 25 | Work areas module (fix `city_id` / `country_id` columns) | 5 | [#104](https://github.com/mahmoud-almalah/employee-manager-api/pull/104) |
| 26 | Work shifts module | 3 | [#105](https://github.com/mahmoud-almalah/employee-manager-api/pull/105) |
| 27 | Supervisors module | 3 | [#106](https://github.com/mahmoud-almalah/employee-manager-api/pull/106) |
| 28 | Wire organization relations into the User entity | 2 | [#107](https://github.com/mahmoud-almalah/employee-manager-api/pull/107) |
| 30 | Sprint 2 documentation and release notes | 2 | this release |

Committed 25 points, delivered 25 (the epic #21 has no estimate). The test suite grew from 98 to 231 tests, and the Sprint 1 retro action "real factories for the organization tables" was done inside the module tasks (`CountryFactory`, `CityFactory`, `PositionFactory`, `WorkAreaFactory`, `WorkShiftFactory`, `SupervisorFactory`); `organization()` in `tests/Pest.php` now only composes them.

### Scope changes during the sprint

- **#26** Two acceptance criteria were read together: "end time after start time" and "overnight shifts allowed" became "an end earlier than the start means the shift runs past midnight; equal times are rejected". Overlap is checked on a 24-hour clock. The reading is written on the issue.
- **#27** `supervisors.status` was a `mediumInteger`. To back it with an enum the way users are, a new migration turns it into a string (the table was unused). Noted on the issue.
- **#28** Beyond the criteria, user create and update also reject a shift that belongs to another work area, for the same reason the city must belong to the country.
- **#22** Country names must be unique (not in the criteria, but duplicates make no sense for a reference list).
- **#25** The "verified with migrate:fresh" criterion was met on a throwaway SQLite database (fresh, rollback, migrate). It was **not** run on MySQL: the credentials in the local `.env` were rejected, and the run did not touch the owner's development database.
- Unplanned: a `BusinessConflictException` base class rendered as 409, which several modules needed (country in use, overlapping shift).

## Review

What can be demonstrated, signed in as `admin` after `migrate --seed`:
- Create a country, a city in it, a position, a work area (manager must be an active user, city must belong to the country) and shifts for it. A second shift that overlaps the first answers 409; a 22:00-06:00 shift is accepted next to a 06:00-14:00 one.
- `GET /api/users?include=country,city,position,workArea,workShift` returns a user with its whole organization, using one query per relation and not one per user.
- Deleting a country, city, position, work area or shift that users still reference answers 409 instead of cascading.
- Roles are enforced from the existing matrix: admin writes everything, HR writes positions and reads the rest, managers write shifts, employees see none of it.

Not in this sprint: `DatabaseSeeder` still creates users without organization data, so a seeded database has none until an organization seeder exists.

## Retrospective

**Went well**
- With `User` as the reference slice, each module was mostly mechanical: the same set of commands, queries, handlers, request, resource, factory and tests. Three of the modules passed their new tests on the first run.
- Putting the overlap rule in a small value object (`ShiftWindow`) made it testable without the framework, including the overnight cases that are easy to get wrong.
- Replacing raw inserts with factories in the test helper made every later test shorter.

**Did not go well**
- A shell `sed` with backslash namespaces produced a broken import (`PresentationResourcesApiResource`) once, and a copy script created stray files once. Both were caught by looking at the output before committing, not by tooling; the Sprint 1 note about avoiding `sed` with namespaces was right and still got ignored.
- PHPStan found a typing problem in `WorkShiftRepository` after the commits were made, so that PR carries an extra fix commit.
- Very long shell commands were truncated and had to be re-sent in smaller pieces.
- The one acceptance criterion that could not be verified honestly, migrations on MySQL, is only covered by SQLite. CI also uses SQLite, so a MySQL-only problem would not be caught anywhere.

**Actions**
- Add a MySQL service to CI (or a second job) so migrations are checked on the database the README tells people to use.
- Sprint 3: add an organization seeder so `migrate --seed` produces a usable dataset, and re-check the permission matrix when attendance arrives (managers currently cannot read countries or cities).
- Raise PHPStan above level 5 once the module count grows (carried over from Sprint 1).
- Review the Sprint 2 pull requests as the owner; they were merged on green CI without a human review.

## Velocity

25 points were delivered in about 40 minutes of wall-clock time, including roughly one minute of CI per pull request. That is far above a human team's week and should not be used to plan Sprint 3. The honest baseline is still unknown: Sprint 1 (53 points) and Sprint 2 (25 points) were both executed in single sessions by an AI pair programmer.

## Attribution

Sprint 2 was planned by the owner (backlog, estimates and acceptance criteria) and implemented by Claude Code in a single unattended scheduled session, without the owner present. The owner has not reviewed it yet and the ADR status rules still apply: nothing here is an owner decision until it is reviewed.

Choices that Claude made while implementing, not taken from the issues:
- `ApiResource` as the shared resource envelope, and discovering route files with a glob.
- `BusinessConflictException` and the 409 convention for deletes that would orphan data.
- `ShiftWindow` as the overlap model (minutes since midnight, segments across midnight) and the reading of the shift criteria described above.
- A single `Organization` presentation module for the six reference entities, and one request class for both create and update.
- The extra rules: unique country names and shifts that must belong to the user's work area.
