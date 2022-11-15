#!/usr/bin/env sh

# Install WordPress.
wp core install \
  --title="Project" \
  --admin_user="wordpress" \
  --admin_password="wordpress" \
  --admin_email="admin@example.com" \
  --url="http://axeptio-wordpress-plugin.test" \
  --skip-email

# Update permalink structure.
wp option update permalink_structure "/%year%/%monthnum%/%postname%/" --skip-themes --skip-plugins

# Activate plugin.
# wp plugin activate axeptio-wordpress-plugin

# install youtube embed: https://wordpress.org/plugins/youtube-embed-plus/
wp plugin install youtube-embed-plus --activate
