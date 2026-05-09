<?php
/**
 * Product meta schema definitions.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\ProductMeta;

defined( 'ABSPATH' ) || exit;

/**
 * Returns a normalized meta schema list.
 */
class ProductMetaSchema {
	/**
	 * Get product meta schemas.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function get() {
		return array(
			'_mr_current_coa_id' => array(
				'type'              => 'integer',
				'single'            => true,
				'sanitize_callback' => 'absint',
				'show_in_rest'      => array(
					'schema' => array(
						'type' => 'integer',
					),
				),
			),
			'_mr_product_form'   => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'_mr_ingredients'    => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'show_in_rest'      => true,
			),
			'_mr_suggested_use'  => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'show_in_rest'      => true,
			),
			'_mr_warning'        => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'show_in_rest'      => true,
			),
		);
	}
}
