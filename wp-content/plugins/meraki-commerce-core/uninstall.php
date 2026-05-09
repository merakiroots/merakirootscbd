<?php
/**
 * Uninstall routines for Meraki Commerce Core.
 *
 * @package MerakiCommerceCore
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'meraki_commerce_core_version' );
