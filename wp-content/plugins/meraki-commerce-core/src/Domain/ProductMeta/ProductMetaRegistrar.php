<?php
/**
 * Product meta registration.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\ProductMeta;

defined( 'ABSPATH' ) || exit;

/**
 * Registers product meta fields.
 */
class ProductMetaRegistrar {
	/**
	 * Register meta definitions for products.
	 *
	 * @return void
	 */
	public function register() {
		foreach ( ProductMetaSchema::get() as $meta_key => $args ) {
			register_post_meta( 'product', $meta_key, $args );
		}
	}
}
