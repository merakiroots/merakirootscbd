<?php
/**
 * COA post type registration.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\COA;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the COA post type.
 */
class CoaPostType {
	/**
	 * Post type slug.
	 */
	const POST_TYPE = 'mr_coa';

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => array(
					'name'               => __( 'COAs', 'meraki-commerce-core' ),
					'singular_name'      => __( 'COA', 'meraki-commerce-core' ),
					'add_new_item'       => __( 'Add New COA', 'meraki-commerce-core' ),
					'edit_item'          => __( 'Edit COA', 'meraki-commerce-core' ),
					'new_item'           => __( 'New COA', 'meraki-commerce-core' ),
					'view_item'          => __( 'View COA', 'meraki-commerce-core' ),
					'search_items'       => __( 'Search COAs', 'meraki-commerce-core' ),
					'not_found'          => __( 'No COAs found.', 'meraki-commerce-core' ),
					'not_found_in_trash' => __( 'No COAs found in Trash.', 'meraki-commerce-core' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-media-document',
				'supports'     => array( 'title', 'excerpt' ),
				'rewrite'      => false,
			)
		);
	}
}
