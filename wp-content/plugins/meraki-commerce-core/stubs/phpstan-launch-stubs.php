<?php
namespace {
	/**
	 * Minimal launch-analysis stubs for runtime symbols supplied by WordPress,
	 * WooCommerce, WP-CLI, and the plugin loader.
	 *
	 * @package MerakiCommerceCore
	 */

	if ( ! defined( 'MERAKI_COMMERCE_CORE_FILE' ) ) {
		define( 'MERAKI_COMMERCE_CORE_FILE', __DIR__ . '/../meraki-commerce-core.php' );
	}

	if ( ! defined( 'MERAKI_COMMERCE_CORE_DIR' ) ) {
		define( 'MERAKI_COMMERCE_CORE_DIR', __DIR__ . '/../' );
	}

	if ( ! defined( 'MERAKI_COMMERCE_CORE_URL' ) ) {
		define( 'MERAKI_COMMERCE_CORE_URL', 'http://localhost/wp-content/plugins/meraki-commerce-core/' );
	}

	if ( ! defined( 'MERAKI_COMMERCE_CORE_VERSION' ) ) {
		define( 'MERAKI_COMMERCE_CORE_VERSION', '0.1.0' );
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		class WooCommerce {}
	}

	if ( ! class_exists( 'WC_Product' ) ) {
		class WC_Product {
			public function get_id(): int {
				return 0;
			}
		}
	}

	if ( ! class_exists( 'WP_CLI' ) ) {
		class WP_CLI {
			/**
			 * @param string $name     Command name.
			 * @param mixed  $callable Command callable.
			 * @return void
			 */
			public static function add_command( string $name, $callable ): void {}

			public static function success( string $message ): void {}

			public static function warning( string $message ): void {}

			public static function log( string $message ): void {}
		}
	}

	if ( ! function_exists( 'woocommerce_wp_select' ) ) {
		/**
		 * @param array<string,mixed> $field Field args.
		 * @return void
		 */
		function woocommerce_wp_select( array $field ): void {}
	}
}

namespace WP_CLI\Utils {
	/**
	 * @param array<string,mixed> $assoc_args Arguments.
	 * @param string              $name       Flag name.
	 * @param mixed               $default    Default value.
	 * @return mixed
	 */
	function get_flag_value( array $assoc_args, string $name, $default = null ) {
		return $assoc_args[ $name ] ?? $default;
	}
}
