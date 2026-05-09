<?php
/**
 * REST field exposure helpers.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\Rest;

use MerakiCommerceCore\Domain\COA\CoaPostType;
use MerakiCommerceCore\Domain\COA\CoaRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Exposes normalized fields on product and COA REST responses.
 */
class RestFields {
	/**
	 * Repository dependency.
	 *
	 * @var CoaRepository
	 */
	private $repository;

	/**
	 * Constructor.
	 *
	 * @param CoaRepository $repository Repository instance.
	 */
	public function __construct( CoaRepository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Register custom REST fields.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_field(
			'product',
			'meraki_current_coa',
			array(
				'get_callback' => array( $this, 'get_product_coa' ),
				'schema'       => array(
					'type'     => array( 'object', 'null' ),
					'readonly' => true,
				),
			)
		);

		register_rest_field(
			CoaPostType::POST_TYPE,
			'meraki_public_data',
			array(
				'get_callback' => array( $this, 'get_coa_public_data' ),
				'schema'       => array(
					'type'     => array( 'object', 'null' ),
					'readonly' => true,
				),
			)
		);
	}

	/**
	 * Get current product COA data.
	 *
	 * @param array<string,mixed> $object REST object data.
	 * @return array<string,mixed>|null
	 */
	public function get_product_coa( $object ) {
		$product_id = isset( $object['id'] ) ? absint( $object['id'] ) : 0;
		$coa_post   = $this->repository->get_current_for_product( $product_id );

		return $coa_post ? $this->repository->get_public_data( $coa_post->ID ) : null;
	}

	/**
	 * Get normalized public COA data.
	 *
	 * @param array<string,mixed> $object REST object data.
	 * @return array<string,mixed>|null
	 */
	public function get_coa_public_data( $object ) {
		$coa_id = isset( $object['id'] ) ? absint( $object['id'] ) : 0;
		return $coa_id ? $this->repository->get_public_data( $coa_id ) : null;
	}
}
