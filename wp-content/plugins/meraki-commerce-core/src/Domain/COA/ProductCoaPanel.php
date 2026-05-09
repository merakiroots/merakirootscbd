<?php
/**
 * Product admin COA selector.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\COA;

defined( 'ABSPATH' ) || exit;

/**
 * Adds COA controls to WooCommerce product editing.
 */
class ProductCoaPanel {
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
	 * Render the current COA field.
	 *
	 * @return void
	 */
	public function render() {
		global $post;

		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		wp_nonce_field( 'meraki_commerce_core_save_product_coa', 'meraki_commerce_core_product_coa_nonce' );

		$current_coa_id = absint( get_post_meta( $post->ID, '_mr_current_coa_id', true ) );
		$options        = array( '' => __( 'Select a COA', 'meraki-commerce-core' ) );

		foreach ( $this->repository->all() as $coa_post ) {
			$options[ $coa_post->ID ] = sprintf(
				'%1$s (#%2$d)',
				$coa_post->post_title,
				$coa_post->ID
			);
		}

		woocommerce_wp_select(
			array(
				'id'          => '_mr_current_coa_id',
				'label'       => __( 'Current COA', 'meraki-commerce-core' ),
				'description' => __( 'Choose the active COA record for this product.', 'meraki-commerce-core' ),
				'desc_tip'    => true,
				'value'       => $current_coa_id,
				'options'     => $options,
			)
		);
	}

	/**
	 * Save the COA selection.
	 *
	 * @param int $post_id Product ID.
	 * @return void
	 */
	public function save( $post_id ) {
		if ( ! isset( $_POST['meraki_commerce_core_product_coa_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['meraki_commerce_core_product_coa_nonce'] ) ), 'meraki_commerce_core_save_product_coa' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$coa_id = isset( $_POST['_mr_current_coa_id'] ) ? absint( wp_unslash( $_POST['_mr_current_coa_id'] ) ) : 0;

		update_post_meta( $post_id, '_mr_current_coa_id', $coa_id );

		if ( $coa_id ) {
			$product_ids   = CoaNormalizer::normalize_product_ids( get_post_meta( $coa_id, '_mr_coa_related_product_ids', true ) );
			$product_ids[] = $post_id;
			update_post_meta( $coa_id, '_mr_coa_related_product_ids', array_values( array_unique( array_filter( $product_ids ) ) ) );
		}
	}
}
