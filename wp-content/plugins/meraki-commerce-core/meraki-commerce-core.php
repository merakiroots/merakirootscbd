<?php
/**
 * Plugin Name: Meraki Commerce Core
 * Description: Product trust metadata, COA records, and lab-results rendering for the Meraki Roots storefront.
 * Version: 0.1.0
 * Author: OpenAI
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Text Domain: meraki-commerce-core
 *
 * @package MerakiCommerceCore
 */

defined( 'ABSPATH' ) || exit;

define( 'MERAKI_COMMERCE_CORE_VERSION', '0.1.0' );
define( 'MERAKI_COMMERCE_CORE_FILE', __FILE__ );
define( 'MERAKI_COMMERCE_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MERAKI_COMMERCE_CORE_URL', plugin_dir_url( __FILE__ ) );

spl_autoload_register(
	static function ( $class ) {
		$prefix = 'MerakiCommerceCore\\';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative_class = substr( $class, strlen( $prefix ) );
		$path           = MERAKI_COMMERCE_CORE_DIR . 'src/' . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
);

\MerakiCommerceCore\Bootstrap::boot();
