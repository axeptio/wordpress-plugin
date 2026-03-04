# Improvements

Issues, risks, and proposals identified during the deployment workflow review.

## Critical

| #   | Area     | Issue                                             | Impact                                                      |
| :-- | :------- | :------------------------------------------------ | :---------------------------------------------------------- |
| 1   | Workflow | No CI on PRs or `develop` branch. Tests and linting | Regressions can be merged undetected. Issues are only      |
|     |          | only run at tag push (release time).              | caught at release time, too late to revert cleanly.         |
| 2   | Workflow | Node.js version mismatch: CI uses Node 16,        | Builds may behave differently locally vs CI. Node 16 is EOL |
|     |          | `.nvmrc` and `volta` pin Node 18.                 | since Sept 2023.                                            |

## High

| #   | Area     | Issue                                             | Recommendation                                              |
| :-- | :------- | :------------------------------------------------ | :---------------------------------------------------------- |
| 3   | Workflow | PHP version mismatch: CI uses PHP 7.4 only, but  | Add a matrix build testing PHP 7.4 and 8.0/8.1 to match    |
|     |          | `composer.json` declares `^7.4 \|\| ^8.0`.       | the declared support range.                                 |
| 4   | Workflow | Outdated GitHub Actions: `actions/checkout@v2`,   | Upgrade to `@v4` for checkout and setup-node,              |
|     |          | `actions/setup-node@v2`, `shivammathur/setup-php@v2` | `shivammathur/setup-php@v2` is current but verify SHA.  |
| 5   | Workflow | `.gitlab-ci.yml` still present after migration to | Remove the file. It is not executed by GitHub Actions and   |
|     |          | GitHub Actions. Only ran PHPCS and ESLint.        | misleads contributors about active CI pipelines.            |
| 6   | Workflow | Single job for all steps (lint → test → build →   | Split into separate jobs: `lint`, `test`, `build`, `deploy` |
|     |          | deploy). No parallelization or independent        | with `needs:` dependencies. Speeds up feedback and allows   |
|     |          | failure stages.                                   | re-running only failed stages.                              |

## Medium

| #   | Area     | Issue                                             | Recommendation                                              |
| :-- | :------- | :------------------------------------------------ | :---------------------------------------------------------- |
| 7   | Security | `10up/action-wordpress-plugin-deploy@stable` not  | Pin to a specific version tag or commit SHA for supply      |
|     |          | pinned to a version or SHA.                       | chain security.                                             |
| 8   | Security | `softprops/action-gh-release@v1` not pinned to    | Pin to a commit SHA.                                        |
|     |          | a SHA.                                            |                                                             |
| 9   | Workflow | `yarn install` without `--frozen-lockfile`. Could | Use `yarn install --frozen-lockfile` in CI to ensure        |
|     |          | silently upgrade packages.                        | reproducible installs.                                      |
| 10  | Workflow | `vendor/bin/pest` hardcoded instead of            | Use `composer run test` to stay consistent with             |
|     |          | `composer run test`.                              | `composer.json` scripts.                                    |
| 11  | Workflow | No deployment dry-run capability. Every tag push  | Add a manual `workflow_dispatch` trigger that runs          |
|     |          | goes directly to WordPress.org.                   | lint/test/build but skips SVN deploy.                       |
| 12  | Security | SVN credentials (`SVN_USERNAME`, `SVN_PASSWORD`)  | Document a secret rotation policy and ensure secrets are    |
|     |          | have no documented rotation policy.               | scoped to the minimum required permissions.                 |

## Low

| #   | Area     | Issue                                             | Recommendation                                              |
| :-- | :------- | :------------------------------------------------ | :---------------------------------------------------------- |
| 13  | Workflow | Taskfile.yml `release` task duplicates the SVN    | Clarify in documentation that the Taskfile task is for      |
|     |          | deploy logic of the GitHub Action. Risk of manual | manual emergency use only. Consider removing or renaming    |
|     |          | SVN pushes bypassing CI checks.                   | to `release:manual`.                                        |
| 14  | Workflow | `exclusions.txt` not documented. Unclear which    | Add inline comments to `exclusions.txt` or reference it    |
|     |          | files are excluded from the SVN package and why.  | in this documentation.                                      |
| 15  | Workflow | `composer install` in CI has no `--no-interaction` | Add `--no-interaction` and `--prefer-dist` flags to         |
|     |          | flag. May prompt or behave unexpectedly.          | `composer install` in CI steps.                             |
