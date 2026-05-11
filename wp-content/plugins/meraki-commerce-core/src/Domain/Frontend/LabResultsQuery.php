<?php
/**
 * COA query layer for lab-results screens.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\Frontend;

use MerakiCommerceCore\Domain\COA\CoaPostType;
use MerakiCommerceCore\Domain\COA\CoaRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Builds focused lab-results queries.
 */
class LabResultsQuery {
	/**
	 * Repository instance.
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
	 * Fetch grouped COA data.
	 *
	 * @param array<string,mixed> $atts Shortcode or block attributes.
	 * @return array<string,array<int,array<string,mixed>>>
	 */
	public function get_grouped_results( array $atts = array() ) {
		$status = isset( $atts['status'] ) ? sanitize_key( $atts['status'] ) : 'current';

		$query = new \WP_Query(
			array(
				'post_type'      => CoaPostType::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_key'       => '_mr_coa_status',
				'meta_value'     => $status,
			)
		);

		$grouped = array();

		foreach ( $query->posts as $coa_post ) {
			$data = $this->repository->get_public_data( $coa_post->ID );

			if ( ! $data ) {
				continue;
			}

			if ( empty( $data['attachment_url'] ) ) {
				continue;
			}

			$label = __( 'Unassigned COAs', 'meraki-commerce-core' );

			if ( ! empty( $data['product_ids'] ) ) {
				$first_product = get_post( $data['product_ids'][0] );
				if ( $first_product ) {
					$label                 = $this->get_group_label_for_product( absint( $first_product->ID ) );
					$data['product_title'] = get_the_title( $first_product );
					$data['product_url']   = get_permalink( $first_product );
				}
			}

			if ( ! isset( $grouped[ $label ] ) ) {
				$grouped[ $label ] = array();
			}

			$grouped[ $label ][] = $data;
		}

		return $this->sort_grouped_results( $grouped );
	}

	/**
	 * Get the storefront lab-results group label for a product.
	 *
	 * @param int $product_id Product ID.
	 * @return string
	 */
	private function get_group_label_for_product( int $product_id ): string {
		$terms = get_the_terms( $product_id, 'product_cat' );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return __( 'Unassigned COAs', 'meraki-commerce-core' );
		}

		$group_labels = array(
			'body-lotion'          => __( 'Body Lotions', 'meraki-commerce-core' ),
			'body-lotions'         => __( 'Body Lotions', 'meraki-commerce-core' ),
			'topicals'             => __( 'Body Lotions', 'meraki-commerce-core' ),
			'tinctures'            => __( 'Tinctures', 'meraki-commerce-core' ),
			'capsules'             => __( 'Capsules', 'meraki-commerce-core' ),
			'vape-cartridges'      => __( 'Vape Cartridges', 'meraki-commerce-core' ),
			'terpsolate-diamonds'  => __( 'Terpsolate Diamonds', 'meraki-commerce-core' ),
		);

		foreach ( $group_labels as $slug => $label ) {
			foreach ( $terms as $term ) {
				if ( $slug === $term->slug ) {
					return $label;
				}
			}
		}

		$terms = array_values( $terms );
		return $terms[0]->name;
	}

	/**
	 * Sort grouped lab results into the storefront display order.
	 *
	 * @param array<string,array<int,array<string,mixed>>> $grouped Grouped COA rows.
	 * @return array<string,array<int,array<string,mixed>>>
	 */
	private function sort_grouped_results( array $grouped ): array {
		$order  = array(
			__( 'Body Lotions', 'meraki-commerce-core' ),
			__( 'Tinctures', 'meraki-commerce-core' ),
			__( 'Capsules', 'meraki-commerce-core' ),
			__( 'Vape Cartridges', 'meraki-commerce-core' ),
			__( 'Terpsolate Diamonds', 'meraki-commerce-core' ),
		);
		$sorted = array();

		foreach ( $order as $label ) {
			if ( isset( $grouped[ $label ] ) ) {
				$sorted[ $label ] = $grouped[ $label ];
				unset( $grouped[ $label ] );
			}
		}

		foreach ( $grouped as $label => $items ) {
			$sorted[ $label ] = $items;
		}

		return $sorted;
	}
}
