# Improvements

Issues, risks, and proposals identified during the deployment workflow review.

## Critical

| #  | Area     | Issue                                                                         | Impact                                                                          |
| :- | :------- | :---------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 1  | Workflow | No CI on PRs or `develop` — tests and linting only run at tag push            | Regressions merge undetected; issues caught at release time, too late to revert |
| 2  | Workflow | Node.js version mismatch: CI uses Node 16, `.nvmrc` and `volta` pin Node 18   | Builds differ locally vs CI; Node 16 is EOL since Sept 2023                     |

## High

| #  | Area     | Issue                                                                         | Recommendation                                                                  |
| :- | :------- | :---------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 3  | Workflow | PHP version mismatch: CI uses PHP 7.4 only, `composer.json` supports `^7.4 \|\| ^8.0` | Add a matrix build testing PHP 7.4 and 8.x to match declared support range |
| 4  | Workflow | Outdated GitHub Actions: `actions/checkout@v2`, `actions/setup-node@v2`       | Upgrade to `@v4` for checkout and setup-node; verify `setup-php` SHA           |
| 5  | Workflow | `.gitlab-ci.yml` still present after migration to GitHub Actions               | Remove the file — it is no longer active and misleads contributors              |
| 6  | Workflow | Single job for all steps (lint → test → build → deploy), no parallelization   | Split into `lint`, `test`, `build`, `deploy` jobs with `needs:` dependencies   |

## Medium

| #  | Area     | Issue                                                                         | Recommendation                                                                  |
| :- | :------- | :---------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 7  | Security | `10up/action-wordpress-plugin-deploy@stable` not pinned to version or SHA     | Pin to a specific version tag or commit SHA for supply chain security           |
| 8  | Security | `softprops/action-gh-release@v1` not pinned to a SHA                         | Pin to a commit SHA                                                             |
| 9  | Workflow | `yarn install` without `--frozen-lockfile` — may silently upgrade packages    | Use `yarn install --frozen-lockfile` in CI for reproducible installs            |
| 10 | Workflow | `vendor/bin/pest` hardcoded instead of `composer run test`                    | Use `composer run test` to stay consistent with `composer.json` scripts         |
| 11 | Workflow | No dry-run capability — every tag push deploys directly to WordPress.org      | Add a `workflow_dispatch` trigger that runs lint/test/build but skips SVN deploy |
| 12 | Security | SVN credentials (`SVN_USERNAME`, `SVN_PASSWORD`) have no rotation policy      | Document a rotation policy; scope secrets to minimum required permissions       |

## Low

| #  | Area     | Issue                                                                         | Recommendation                                                                  |
| :- | :------- | :---------------------------------------------------------------------------- | :------------------------------------------------------------------------------ |
| 13 | Workflow | Taskfile `release` task duplicates GitHub Action SVN logic — risk of manual bypasses | Clarify it is for emergency use only; consider renaming to `release:manual` |
| 14 | Workflow | `exclusions.txt` has no comments explaining why each path is excluded         | Add inline comments or reference this document                                  |
| 15 | Workflow | `composer install` in CI missing `--no-interaction` flag                      | Add `--no-interaction` and `--prefer-dist` to all `composer install` CI steps   |
