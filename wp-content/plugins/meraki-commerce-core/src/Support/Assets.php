<?php
/**
 * Asset registration helpers.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Registers plugin assets.
 */
class Assets {
	/**
	 * Enqueue admin media helpers on the COA screen.
	 *
	 * @param string $hook_suffix Current admin hook.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || 'mr_coa' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();

		$handle = 'meraki-commerce-core-admin';
		$src    = MERAKI_COMMERCE_CORE_URL . 'assets/admin/admin.js';

		if ( file_exists( MERAKI_COMMERCE_CORE_DIR . 'assets/admin/admin.js' ) ) {
			wp_enqueue_script( $handle, $src, array( 'jquery' ), MERAKI_COMMERCE_CORE_VERSION, true );
		}
	}

	/**
	 * Placeholder for block asset registration.
	 *
	 * @return void
	 */
	public function register_block_assets() {
		// Intentionally small for the first pass. Dynamic rendering happens server-side.
	}
}
