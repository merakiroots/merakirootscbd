#!/usr/bin/env bash
set -euo pipefail

wp theme status meraki-block-theme --allow-root
wp plugin list --status=active --allow-root
printf '\nProduct counts:\n'
wp post list --post_type=product --post_status=publish --format=count --allow-root
wp post list --post_type=product --post_status=draft --format=count --allow-root
printf '\nKey SKUs:\n'
for sku in MR-TIN-300 MR-TIN-2400 MR-CAP-AM MR-CAP-PM MR-VAPE-ZK-500 MR-TERP-ZK MR-TOP-HL-600; do
  wp wc product list --sku="$sku" --format=table --allow-root
done
printf '\nPage URLs:\n'
for path in shop lab-results contact partner faqs cart checkout my-account; do
  wp post list --post_type=page --name="$path" --field=url --allow-root
done
printf '\nLaunch audit:\n'
wp eval-file /var/www/html/scripts/wp-cli/meraki-audit-launch.php --allow-root
wp rewrite flush --allow-root
wp cache flush --allow-root || true
wp litespeed-purge all --allow-root || true
