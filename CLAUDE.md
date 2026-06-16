# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WordPress plugin integrating the Axeptio CMP (Consent Management Platform) SDK for GDPR-compliant cookie consent management. Published on WordPress.org SVN. Supports WPML/PolyLang for multilingual sites.

- **WordPress.org slug**: `axeptio-sdk-integration` (SVN repo, plugin folder, dist package)
- **Text domain**: `axeptio-sdk-integration` (the 2nd arg of every i18n call: `__()`, `esc_html__()`, …)
- **Admin page slug**: `axeptio-wordpress-plugin` — DISTINCT from the text domain. Used in `menu_slug`, `parent_slug`, `'page' => …`, `toplevel_page_…`, `do_settings_sections()`. Do NOT "fix" these to `axeptio-sdk-integration`; renaming would break the admin menu and option saving.
- **Entry point file**: `axeptio-wordpress-plugin.php` (legacy filename, unrelated to the text domain)
- **Min PHP**: 7.4 | **Min WP**: 5.6
- **Constant prefix**: `XPWP_`
- **Global prefixes** (PHPCS enforced): `axeptio`, `AXEPTIO`, `Axeptio`, `XPWP`, `xpwp`

## Development Commands

All task commands use [Taskfile](https://taskfile.dev) and run inside Docker.

```bash
# Docker environment
task build          # Build and start containers
task start / stop   # Start/stop containers
task ssh            # Shell into container

# PHP
task composer-install   # Install all deps (incl. dev)
task composer-build     # Install production deps only (--no-dev --optimize-autoloader)
task lint-php           # PHPCS (WordPress Coding Standards)
task fix-php            # Auto-fix PHPCS violations
task php-stan           # PHPStan static analysis (level max)

# Tests (Pest v1 on PHPUnit, runs inside Docker)
docker-compose run --rm web composer test        # Run all tests
docker-compose run --rm web vendor/bin/pest      # Run all tests directly
docker-compose run --rm web vendor/bin/pest tests/GtmEventsTest.php  # Single test file

# JavaScript / CSS (run on host, uses Volta node 18)
yarn install
yarn build              # One-time dev build (Laravel Mix + Tailwind)
yarn build:production   # Production build
yarn start              # Watch mode with BrowserSync
yarn eslint assets/js/  # ESLint

# Release to WordPress.org SVN
task release -- <version>
```

## Commits

Conventional commits enforced by Husky + commitlint. Allowed types: `build`, `chore`, `ci`, `docs`, `feat`, `fix`, `hotfix`, `perf`, `refactor`, `revert`, `style`, `test`. Prettier runs on staged `.js`, `.md`, `.yml`, `.mjs` files via lint-staged.

## Architecture

### Module System

All features extend the abstract `Module` class (`includes/classes/class-module.php`) which requires `can_register()` and `register()` methods. `Module_Initialization` (singleton) auto-discovers and initializes modules sorted by `$load_order`.

Registered modules (in `class-module-initialization.php`):

- `Models` — data layer
- `Activation_Hook` — plugin activation logic
- `AlpineJS_Wpkses` — wp_kses compatibility for Alpine.js attributes
- `Admin\Pages\Admin_Main` — admin settings pages
- `Axeptio_Sdk` — frontend SDK injection
- `Hook_Modifier` — consent-based script blocking
- `Plugins` — REST API for plugin management
- `Wp_Rocket` — WP Rocket cache compatibility
- `Settings` — backend settings registration
- `Sdk_Proxy` — SDK proxy endpoint

### Key Directory Layout

```
axeptio-wordpress-plugin.php   # Entry point, defines constants, bootstraps
includes/
  core.php                     # setup(), init(), activate(), deactivate(), asset enqueue
  helpers/
    helpers.php                # Plugin helpers (Axeptio\Plugin namespace)
    utility.php                # Asset utilities (Axeptio\Plugin\Utility namespace)
  classes/                     # PSR-4 autoloaded under \Axeptio\Plugin\
    admin/                     # Admin pages, settings UI, REST endpoints
    backend/                   # Backend settings registration
    frontend/                  # SDK injection, hook modifier, SDK proxy
    models/                    # Data models (Client ID, Plugins, Settings, SDK, etc.)
    migrations/                # Versioned DB migrations (class-migration-X.Y.Z.php)
    third-party/               # Third-party integrations (WP Rocket)
    compat/                    # Compatibility layers (Alpine.js wp_kses)
    utils/                     # Template engine, migration manager, hook parser
templates/
  admin/                       # Admin PHP templates
  frontend/                   # Frontend PHP templates
assets/
  js/                          # Source JS (backend/app.js, frontend/axeptio.js)
  css/                         # Source CSS (backend uses Tailwind, frontend plain PostCSS)
dist/                          # Built assets (generated, not committed)
```

### Frontend Stack

- **JS**: Alpine.js with `@alpinejs/persist`
- **CSS**: Tailwind CSS (admin only, scoped to `#axeptio-app` via `important`), PostCSS with nesting
- **Build**: Laravel Mix (Webpack), outputs to `dist/`

### Coding Standards

- **PHP**: WordPress Coding Standards (WPCS) via PHPCS. Short array syntax `[]` enforced. PHP 7.4+ features allowed.
- **JS**: WordPress ESLint plugin
- **Static analysis**: PHPStan at max level with WordPress extensions
- **Direct-access guard**: every file under `templates/` must start with `defined( 'ABSPATH' ) || exit;` (Plugin Check `missing_direct_file_access_protection`). For HTML-first templates, prepend `<?php defined( 'ABSPATH' ) || exit; ?>`. Class files under `includes/` that only declare a namespaced class are not flagged. `rector.php` and `tests/` are flagged locally but excluded from the dist package via `.distignore`, so leave them alone.
- **Plugin Check**: `ddev wp plugin check axeptio-sdk-integration` (the WordPress.org review tool). The dist package is produced from `.distignore` (CI/CD deploy); note `exclusions.txt` used by `task release` does NOT currently exclude `tests/`.
