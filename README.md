# Axeptio WordPress Plugin

Integrate Axeptio SDK to your Wordpress Website

## Contents

- `.gitignore`. Used to exclude certain files from the repository.
- `README.md`. The file that you’re currently reading.
- A `axeptio-wordpress-plugin` directory that contains the source code - a fully executable WordPress plugin.
  - This folder can be zipped and upload to test on your wordpress installation

## Features

- The plugin is based on the [Plugin API](http://codex.wordpress.org/Plugin_API), [Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards), and [Documentation Standards](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/).
- All classes, functions, and variables are documented so that you know what you need to change.

## Installation

The plugin can be installed directly into your plugins folder "as-is".

## Development

For development, Taskfile (https://taskfile.dev) is used to simplify various necessary operations.

- `task release -- [version]`: Creates a new release of the extension. The task performs the necessary build tasks (composer & yarn), synchronizes the required files, updates the SVN repository with the new version, publishes the release on WordPress.org, and finally cleans up temporary files.
- `task build`: Builds the application services in Docker containers.
- `task restart`: Restarts the Docker application by first stopping services with stop, then starting them with start.
- `task start`: Starts the Docker application.
- `task stop`: Stops the Docker application.
- `task logs`: Displays the application logs.
- `task clean-modules`: Removes the vendor/ and node_modules/ directories inside the Docker container.
- `task ssh`: Opens an SSH session in the Docker container. Use task ssh -- root to connect as root.
- `task composer-install`: Installs Composer packages inside the Docker container.
- `task composer-build`: Installs Composer packages without development dependencies and optimizes the autoloader.
- `task composer-require`: Installs a specific Composer package.
- `task php-stan`: Performs a static analysis of the PHP code.
- `task lint-php`: Checks the PHP code's compliance with WP Coding Standards.
- `task fix-php`: Automatically fixes PHP coding problems detected by the linter.
- `task eslint`: Checks the JavaScript code's compliance with ESLint rules.
- `task eslint-fix`: Automatically fixes JavaScript coding problems detected by the linter.

[Plugin Guidelines best practices](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)

[Plugin Handbook](https://developer.wordpress.org/plugins/)
