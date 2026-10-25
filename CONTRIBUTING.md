# Contributing

This is a solo project, but it is run like a team project: every change starts from an issue, goes through a pull request, and passes CI before it is merged.

## Branches

| Branch | Purpose |
| --- | --- |
| `master` | Production. Only receives merges from `staging`, each one tagged as a release. |
| `staging` | Release candidate. Receives merges from `develop` at the end of a sprint. |
| `develop` | Integration branch and default. All feature PRs target it. |
| `feature/<issue>-<slug>` | One task. Branched from `develop`, squash-merged back, then deleted. |

Hotfixes use `hotfix/<issue>-<slug>` from `master` and are merged back into `develop`.

## Workflow

1. Pick an issue from the current sprint on the [project board](https://github.com/users/mahmoud-almalah/projects/5) and move it to **In Progress**.
2. Create `feature/<issue>-<slug>` from `develop`.
3. Commit in small steps using [Conventional Commits](https://www.conventionalcommits.org): `feat(user): add CreateUser command`, `fix(repo): build a fresh query per call`, `docs: ...`, `test: ...`, `refactor: ...`, `chore: ...`.
4. Open a PR to `develop` using the template, with `Closes #<issue>`. The issue moves to **In Review**.
5. When CI is green, squash-merge. The issue closes and moves to **Done**.

## Definition of Ready
- Has a user story and acceptance criteria
- Estimated in story points, labelled by layer and priority
- Small enough to finish in a few days

## Definition of Done
- Acceptance criteria met
- Pest tests added for new behaviour
- `pint`, `phpstan` and `pest` pass in CI
- Docs and `CHANGELOG.md` updated where relevant
- PR squash-merged into `develop`

## Architecture rules
- Dependencies point inward: Presentation → Application → Domain. Infrastructure implements contracts defined in Application.
- Controllers never touch Eloquent directly; they dispatch commands and ask queries through the buses.
- Business rules live in the Domain and Application layers, not in controllers or migrations.

## Local checks
```bash
vendor/bin/pint
vendor/bin/phpstan analyse
vendor/bin/pest
```
