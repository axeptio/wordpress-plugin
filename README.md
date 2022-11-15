# Axeptio WordPress Plugin V2

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

[Plugin Guidelines best practices](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)

[Plugin Handbook](https://developer.wordpress.org/plugins/)

# Develop with docker

## Set up

1. Clone this repo
2. Add `axeptio-wordpress-plugin.test` to `/etc/hosts`, e.g.:
   ```
   127.0.0.1 localhost axeptio-wordpress-plugin.test
   ```

## Start environment

Ensure the .env file is present

```sh
docker-compose up -d
```

To view logs for a
specific container, use `docker-compose logs [container]`, e.g.:

```sh
docker-compose logs wordpress
```

## Install WordPress

```sh
docker-compose run --rm wp-cli install-wp
```

Log in to `http://project.test/wp-admin/` with `wordpress` / `wordpress`.

Alternatively, you can navigate to `http://project.test/` and manually perform
the famous five-second install.

## WP-CLI

You will probably want to [create a shell alias][3] for this:

```sh
docker-compose run --rm wp-cli wp [command]
```

Import to and export from the WordPress database:

```sh
docker-compose run --rm wp-cli wp db import - < dump.sql
docker-compose run --rm wp-cli wp db export - > dump.sql
```

## TODO Running tests (PHPUnit)

## Troubleshooting

Check if an element of the stack is up:

```
docker-compose ps
```

If you updated some config file, .env or any docker file and the changes are not showing, recreate volumes and containers with the following options:

```
/!\ DATA WILL BE LOST /!\

docker-compose --force-recreate -V
```

Recreate anonymous volumes instead of retrieving data from the previous containers: `--renew-anon-volumes , -V`

Recreate containers even if their configuration and image haven't changed: `--force-recreate`
