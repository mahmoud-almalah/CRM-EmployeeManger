# Security Policy

## Supported versions

Only the latest release on `master` receives security fixes. This is a portfolio project that is developed in sprints; older tags are not patched.

| Version | Supported |
| --- | --- |
| latest release | yes |
| anything older | no |

## Reporting a vulnerability

Please **do not open a public issue or discussion** for a security problem.

Report it privately through GitHub: open the repository's **Security** tab and choose **Report a vulnerability**. Include what you found, how to reproduce it (a request and response is ideal), and which version or commit you tested.

You can expect an acknowledgement within a few days and a fix or a clear explanation afterwards. This is a one-person project, so there is no bounty, but credit in the release notes is offered if you want it.

## Scope

In scope: the API in this repository (authentication, authorization, input handling, data exposure) and its CI configuration.

Out of scope: findings that need a modified or compromised host, social engineering, denial of service by sheer traffic volume, and vulnerabilities in third-party packages that are already tracked by Dependabot and have no fix yet.

## How security is handled here

- **Dependencies:** Dependabot opens update pull requests weekly, and CI runs `composer audit` on every push and pull request and once a week. A build with a known-vulnerable dependency shows up red.
- **Secrets:** GitHub secret scanning and push protection are on. `.env`, Passport keys and `auth.json` are git-ignored.
- **Branches:** `master`, `staging` and `develop` only accept changes through pull requests with passing CI, and cannot be force-pushed or deleted.
- **Authentication:** Passport personal access tokens; the login route is rate-limited; deleting or deactivating a user revokes their tokens.
- **Authorization:** role and permission checks on every protected route (see the wiki page on roles and permissions).
