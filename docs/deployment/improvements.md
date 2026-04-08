# Improvements

Issues, risks, and proposals identified during the deployment workflow review.

## Critical

| #  | Area     | Issue                                                                                   | Impact                                                                          |
| :- | :------- | :-------------------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 1  | Workflow | No CI on PRs or `develop` — tests and linting only run at tag push                     | Regressions merge undetected; issues caught at release time, too late to revert |

## High

| #  | Area     | Issue                                                                                   | Recommendation                                                                  |
| :- | :------- | :-------------------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 3  | Workflow | PHP version mismatch: CI uses PHP 7.4 only; `composer.json` supports `^7.4 \|\| ^8.0` | Add a matrix build testing PHP 7.4 and 8.x to match declared support range      |
| 5  | Workflow | `.gitlab-ci.yml` still present after migration to GitHub Actions                       | Remove the file; it is no longer active and misleads contributors               |

## Medium

| #  | Area     | Issue                                                                                   | Recommendation                                                                  |
| :- | :------- | :-------------------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 12 | Security | SVN credentials (`SVN_USERNAME`, `SVN_PASSWORD`) have no rotation policy                | Document a rotation policy; scope secrets to minimum required permissions       |
| 13 | Workflow | Taskfile `release` task duplicates GitHub Action SVN logic — risk of manual bypasses   | Clarify it is for emergency use only; consider renaming to `release:manual`     |

## Low

| #  | Area     | Issue                                                                                   | Recommendation                                                                  |
| :- | :------- | :-------------------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 14 | Workflow | `exclusions.txt` has no inline comments explaining why each path is excluded            | Add inline comments, or reference this document from the file                   |

## New Findings

Issues identified during the review of the CI/CD rework (PRs #47, #48, #50).

| #  | Area     | Issue                                                                                                                                                                                                                    | Recommendation                                                                                                                                                          |
| :- | :------- | :----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | :---------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 18 | Workflow | `workflow_dispatch` with `dry_run=false` on a non-tag ref skips the deploy step entirely (condition: `startsWith(github.ref, 'refs/tags/') \|\| inputs.dry_run`); the `version` input is still required but unused in that case | Document that `workflow_dispatch` is intended for dry-run testing only; consider removing `dry_run=false` as a supported option or clarifying its purpose in the workflow |

## Fixed in CI/CD Rework (PRs #47, #48, #50)

Resolved by Olivier (ogorzalka).

| #  | Area     | Issue                                                                                   | Fix                                                                             |
| :- | :------- | :-------------------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 2  | Workflow | Node.js version mismatch: CI used Node 16; `.nvmrc` and `volta` pin Node 18             | Node 18 now used in CI; Node 16 was EOL since September 2023                   |
| 4  | Workflow | Outdated GitHub Actions: `checkout@v2`, `setup-node@v2`                                 | Upgraded: `checkout@v4`, `setup-node@v4`, `setup-php@2.37.0`                  |
| 6  | Workflow | Single job for all steps — no separation between lint, test, build, and deploy          | Split into `lint-and-test` and `deploy` jobs with `needs:` dependency          |
| 7  | Security | `10up/action-wordpress-plugin-deploy@stable` not pinned to a version or SHA             | Pinned to `@2.3.0`                                                             |
| 8  | Security | `softprops/action-gh-release@v1` not pinned to a SHA                                   | Pinned to `@v2.2.1`                                                            |
| 9  | Workflow | `yarn install` without `--frozen-lockfile` — could silently upgrade packages            | Changed to `yarn install --immutable`; fails if lockfile is out of sync        |
| 10 | Workflow | `vendor/bin/pest` hardcoded instead of `composer run test`                              | Changed to `composer test`                                                     |
| 11 | Workflow | No dry-run capability — every tag push deployed directly to WordPress.org               | Added `workflow_dispatch` trigger with `dry_run` boolean input (defaults to `true`) |
| 15 | Workflow | `composer install` in CI missing `--no-interaction` flag                                | Added `--no-interaction --prefer-dist` to all `composer install` CI steps      |

## Fixed in PR #45 and Follow-up

| #  | Area      | Issue                                                                                                                                                                  | Fix                                                          |
| :- | :-------- | :--------------------------------------------------------------------------------------------------------------------------------------------------------------------- | :----------------------------------------------------------- |
| 16 | Packaging | `docs/` directory was not in `exclusions.txt` — internal engineering docs would be shipped in the WordPress.org SVN package and end-user `.zip` via the manual release | Added `docs/` to `exclusions.txt` (PR #45)                   |
| 17 | Packaging | `docs/` directory was not in `.distignore` — internal engineering docs would be shipped in the CI-generated SVN package and end-user `.zip`                           | Added `/docs` to `.distignore`                               |
