<h1>
  <img src="https://axeptio.imgix.net/2024/07/e444a7b2-ea3d-4471-a91c-6be23e0c3cbb.png" alt="Descrizione immagine" width="80" style="vertical-align: middle; margin-right: 10px;" />
  Axeptio WordPress Plugin
</h1>

![Version](https://img.shields.io/badge/version-1.0.0-blue?style=flat-square) ![License](https://img.shields.io/badge/license-MIT-green?style=flat-square) ![WordPress Version](https://img.shields.io/badge/WordPress-%5E5.0-blue?style=flat-square) ![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4-blue?style=flat-square)


Integrate the Axeptio SDK with your WordPress Website for seamless privacy management.

Axeptio transforms your **WordPress** site’s privacy management into a smooth and compliant experience. With its easy-to-install and highly customizable plugin, you can ensure GDPR compliance and transparency without compromising user experience.

## 📑 Table of Contents

1. [Contents](#contents)
2. [Key Features](#key-features)
3. [Installation](#installation)
4. [Features](#features)
5. [Development](#development)
6. [Documentation](#documentation)
7. [Useful Links](#useful-links)

<br><br>

## Contents

- `.gitignore`. Used to exclude certain files from the repository.
- `README.md`. The file that you’re currently reading.
- A `axeptio-wordpress-plugin` directory that contains the source code - a fully executable WordPress plugin.
  - This folder can be zipped and upload to test on your wordpress installation
    <br><br>

## Key Features

- **Quick and Easy Installation**: Integrate Axeptio into your WordPress site in just a few clicks. Simply enter your client ID, and the Axeptio code is automatically integrated.
- **Multilingual Compatibility**: Works seamlessly with WPML and PolyLang, ensuring privacy management is tailored to all your users, no matter their language.
- **Smart Extension Blocking**: Filters and hooks allow you to block extensions by default for enhanced security and compliance.
- **Connection to Vendor Database**: Simplify extension blocking with direct access to a comprehensive database.
- **Automatic Updates**: Stay up-to-date with the latest features and improvements effortlessly.
- **Advanced Customization**: Customize the appearance of the consent widget with options for colors, logo, and text directly from your Axeptio back office.
- **Google Tag Manager Integration**: Easily integrate Axeptio with Google Tag Manager for optimized tag management.
- **Dedicated Support**: Access expert assistance for any questions or support needs.
- **Consent Log**: Easily track and manage user consents through the Axeptio back office.
  Installing this plugin does not automatically make your site GDPR-compliant. Each website uses different cookies, and you must ensure the required configuration is in place. Please follow our documentation for proper configuration.
  <br><br>

## 🔧Installation

### Download and Install the Plugin:

- Upload the `axeptio-wordpress-plugin` folder to the `wp-content/plugins` directory.
- Alternatively, you can zip the folder and upload it through the WordPress admin panel.

### Configuration

- After installation, go to the WordPress admin panel.
- Enter your **client ID** to link your website to your Axeptio account.

### Customization:

- Customize the widget’s appearance (colors, logo, text) via the Axeptio back office.
- Configure and review user consent preferences.

### Multilingual Setup:

- The plugin supports WPML and PolyLang for multilingual sites.
  <br><br>

## ✨Features

- **Axeptio SDK Integration**: The plugin automatically downloads and integrates the Axeptio SDK into your WordPress site.
- **Vendor Database**: Access our comprehensive vendor database to manage extension blocking efficiently.
- **Customizable Consent Widget**: Tailor the widget’s appearance with a variety of customization options.
- **Privacy Settings Dashboard**: All configuration and consent management can be done through your WordPress admin panel or Axeptio back office
  <br><br>

## Development

For developers, the plugin utilizes **Taskfile** to simplify various operations.

### Available Tasks:

- `task release --[version]`: Creates a new release, performing necessary build tasks (composer & yarn), synchronizes files, updates SVN, and publishes the release to WordPress.org.
- `task build`: Builds application services in Docker containers.
- `task restart`: Restarts Docker application services.
- `task start`: Starts Docker application.
- `task stop`: Stops Docker application.
- `task logs`: Displays application logs.
- `task clean-modules`: Removes`vendor/` and `node_modules/` directories inside the Docker container.
- `task ssh`: Opens an SSH session in the Docker container (use task `ssh -- root` to connect as root).
- `task composer-install`: Installs Composer packages inside Docker container.
- `task composer-build`: Installs Composer packages without development dependencies and optimizes autoloader.
- `task composer-require`: Installs specific Composer package.
- `task php-stan`: Performs static analysis of the PHP code.
- `task lint-php`: Checks PHP code compliance with WordPress Coding Standards.
- `task fix-php: Automatically fixes PHP coding problems detected by the linter.
- `task eslint`: Checks JavaScript code compliance with ESLint rules.
- `task eslint-fix`: Automatically fixes JavaScript coding problems detected by the linter.
  <br><br>

## 📚Documentation

For detailed setup, configuration, and troubleshooting, please read our full [Documentation](https://wordpress.org/plugins/axeptio-sdk-integration/).
<br><br>

## Useful Links

- [Plugin API](http://codex.wordpress.org/Plugin_API)
- [Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards)
- [Documentation Standards](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/).

## Contributions

### Commit

Please follow the [conventional commit](https://www.conventionalcommits.org/en/v1.0.0/) to make the history clear.
Husky will check the commit message, if you don't follow the convention, the commit will be rejected.
