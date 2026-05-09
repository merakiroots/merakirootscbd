<?php
/**
 * COA repository helpers.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\COA;

defined( 'ABSPATH' ) || exit;

/**
 * Encapsulates common COA queries.
 */
class CoaRepository {
	/**
	 * Fetch all COA posts for admin selectors.
	 *
	 * @return array<int,\WP_Post>
	 */
	public function all() {
		$posts = get_posts(
			array(
				'post_type'      => CoaPostType::POST_TYPE,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC',
				'numberposts'    => -1,
				'suppress_filters' => false,
			)
		);

		return $posts;
	}

	/**
	 * Get the current COA post for a product.
	 *
	 * @param int $product_id Product ID.
	 * @return \WP_Post|null
	 */
	public function get_current_for_product( $product_id ) {
		$coa_id = absint( get_post_meta( $product_id, '_mr_current_coa_id', true ) );

		if ( ! $coa_id ) {
			return null;
		}

		$post = get_post( $coa_id );
		return ( $post instanceof \WP_Post && CoaPostType::POST_TYPE === $post->post_type ) ? $post : null;
	}

	/**
	 * Create or update a COA post for a migrated product.
	 *
	 * @param int   $product_id     Product ID.
	 * @param array $record         Prepared record.
	 * @param bool  $force_relink   Whether to overwrite current relation.
	 * @return int
	 */
	public function upsert_for_product( $product_id, array $record, $force_relink = false ) {
		$product = get_post( $product_id );

		if ( ! $product ) {
			return 0;
		}

		$existing_id = absint( get_post_meta( $product_id, '_mr_current_coa_id', true ) );
		$coa_id      = $existing_id;

		if ( ! $coa_id ) {
			$coa_id = wp_insert_post(
				array(
					'post_type'   => CoaPostType::POST_TYPE,
					'post_status' => 'publish',
					'post_title'  => sprintf(
						/* translators: %s: product title. */
						__( '%s COA', 'meraki-commerce-core' ),
						get_the_title( $product_id )
					),
				),
				true
			);

			if ( is_wp_error( $coa_id ) ) {
				return 0;
			}
		}

		foreach ( $record as $meta_key => $value ) {
			update_post_meta( $coa_id, $meta_key, $value );
		}

		$product_ids = CoaNormalizer::normalize_product_ids( get_post_meta( $coa_id, '_mr_coa_related_product_ids', true ) );
		$product_ids[] = absint( $product_id );
		update_post_meta( $coa_id, '_mr_coa_related_product_ids', array_values( array_unique( array_filter( $product_ids ) ) ) );

		if ( $force_relink || ! $existing_id ) {
			update_post_meta( $product_id, '_mr_current_coa_id', absint( $coa_id ) );
		}

		return absint( $coa_id );
	}

	/**
	 * Get a normalized public COA data structure for rendering.
	 *
	 * @param int $coa_id COA ID.
	 * @return array<string,mixed>|null
	 */
	public function get_public_data( $coa_id ) {
		$coa = get_post( $coa_id );

		if ( ! $coa || CoaPostType::POST_TYPE !== $coa->post_type ) {
			return null;
		}

		$attachment_id = absint( get_post_meta( $coa_id, '_mr_coa_attachment_id', true ) );

		return array(
			'id'          => $coa_id,
			'title'       => get_the_title( $coa_id ),
			'status'      => (string) get_post_meta( $coa_id, '_mr_coa_status', true ),
			'batch_id'    => (string) get_post_meta( $coa_id, '_mr_coa_batch_id', true ),
			'test_date'   => (string) get_post_meta( $coa_id, '_mr_coa_test_date', true ),
			'lab_name'    => (string) get_post_meta( $coa_id, '_mr_coa_lab_name', true ),
			'attachment_id' => $attachment_id,
			'attachment_url' => $attachment_id ? wp_get_attachment_url( $attachment_id ) : '',
			'product_ids' => CoaNormalizer::normalize_product_ids( get_post_meta( $coa_id, '_mr_coa_related_product_ids', true ) ),
		);
	}
}
