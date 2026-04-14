# Deployment Documentation

Overview of the CI/CD pipeline for the WordPress Plugin (`axeptio-sdk-integration`).

> **Note:** Unlike a server-side application, this plugin has no cloud infrastructure (no AWS, ECS, Terraform).
> It is distributed as a PHP/JS package via the [WordPress.org plugin directory](https://wordpress.org/plugins/axeptio-sdk-integration/)
> using SVN as the distribution channel.

## Table of Contents

| Document                         | Description                                       |
| :------------------------------- | :------------------------------------------------ |
| [Workflows](workflows.md)       | GitHub Actions workflows: triggers, jobs, steps   |
| [Environments](environments.md) | Local dev setup and WordPress.org release process |
| [Improvements](improvements.md) | Flagged issues, risks, and improvement proposals  |
