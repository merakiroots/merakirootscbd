#!/usr/bin/env bash
set -euo pipefail

if ! command -v wp >/dev/null 2>&1; then
  echo "WP-CLI is not available in this shell." >&2
  exit 1
fi

wp core version
wp theme install oceanwp --activate
wp plugin install woocommerce ocean-extra wordpress-seo litespeed-cache redirection --activate
wp theme install ./themes/meraki-oceanwp-child-production-v1.2.0.zip --force
wp theme activate meraki-oceanwp-child
wp rewrite structure '/%postname%/'
wp eval-file ./scripts/wp-cli/meraki-bootstrap.php
wp rewrite flush
wp cache flush || true
wp litespeed-purge all || true
