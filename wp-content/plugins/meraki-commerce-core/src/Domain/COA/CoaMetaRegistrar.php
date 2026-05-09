<?php
/**
 * COA meta registration.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\COA;

defined( 'ABSPATH' ) || exit;

/**
 * Registers COA post meta.
 */
class CoaMetaRegistrar {
	/**
	 * Register post meta for the COA CPT.
	 *
	 * @return void
	 */
	public function register() {
		$schemas = array(
			'_mr_coa_attachment_id'      => array(
				'type'              => 'integer',
				'single'            => true,
				'sanitize_callback' => 'absint',
				'show_in_rest'      => true,
			),
			'_mr_coa_batch_id'           => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'_mr_coa_test_date'          => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => array( CoaNormalizer::class, 'normalize_date' ),
				'show_in_rest'      => true,
			),
			'_mr_coa_lab_name'           => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'_mr_coa_related_product_ids' => array(
				'type'              => 'array',
				'single'            => true,
				'sanitize_callback' => array( CoaNormalizer::class, 'normalize_product_ids' ),
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'integer',
						),
					),
				),
			),
			'_mr_coa_status'             => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => array( CoaNormalizer::class, 'normalize_status' ),
				'show_in_rest'      => true,
			),
			'_mr_coa_legacy_url'         => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => array( CoaNormalizer::class, 'normalize_url' ),
				'show_in_rest'      => true,
			),
		);

		foreach ( $schemas as $meta_key => $args ) {
			register_post_meta( CoaPostType::POST_TYPE, $meta_key, $args );
		}
	}
}
