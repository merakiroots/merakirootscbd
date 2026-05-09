<?php
/**
 * Audit the Meraki Roots launch catalog and commerce wiring.
 *
 * Run with:
 * wp eval-file scripts/wp-cli/meraki-audit-launch.php --allow-root
 *
 * @package MerakiRootsLaunch
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with: wp eval-file scripts/wp-cli/meraki-audit-launch.php\n" );
	exit( 1 );
}

/**
 * Add a failure to the audit result.
 *
 * @param array<int,string> $failures Failure list.
 * @param string            $message  Failure message.
 * @return void
 */
function mr_audit_fail( array &$failures, string $message ): void {
	$failures[] = $message;
}

/**
 * Add a warning to the audit result.
 *
 * @param array<int,string> $warnings Warning list.
 * @param string            $message  Warning message.
 * @return void
 */
function mr_audit_warn( array &$warnings, string $message ): void {
	$warnings[] = $message;
}

/**
 * Get a trimmed post meta value.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @return string
 */
function mr_audit_meta( int $post_id, string $key ): string {
	return trim( (string) get_post_meta( $post_id, $key, true ) );
}

$failures = array();
$warnings = array();

if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product' ) ) {
	mr_audit_fail( $failures, 'WooCommerce is not active.' );
}

if ( ! post_type_exists( 'mr_coa' ) ) {
	mr_audit_fail( $failures, 'Meraki Commerce Core did not register the mr_coa post type.' );
}

if ( 'meraki-block-theme' !== get_stylesheet() ) {
	mr_audit_fail( $failures, 'Active stylesheet is not meraki-block-theme; found ' . get_stylesheet() . '.' );
}

if ( 'meraki-block-theme' !== get_template() ) {
	mr_audit_fail( $failures, 'Active template is not meraki-block-theme; found ' . get_template() . '.' );
}

if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
	mr_audit_fail( $failures, 'Permalink structure is not /%postname%/.' );
}

$required_pages = array(
	'home',
	'shop',
	'lab-results',
	'learn',
	'about',
	'contact',
	'partner',
	'faqs',
	'privacy-policy',
	'refund-policy',
	'shipping-policy',
	'terms-of-service',
	'cart',
	'checkout',
	'my-account',
);

foreach ( $required_pages as $page_path ) {
	$page = get_page_by_path( $page_path );

	if ( ! $page || 'publish' !== get_post_status( $page ) ) {
		mr_audit_fail( $failures, 'Required page is missing or unpublished: ' . $page_path );
	}
}

$woocommerce_page_options = array(
	'woocommerce_shop_page_id'      => 'shop',
	'woocommerce_cart_page_id'      => 'cart',
	'woocommerce_checkout_page_id'  => 'checkout',
	'woocommerce_myaccount_page_id' => 'my-account',
);

foreach ( $woocommerce_page_options as $option => $expected_slug ) {
	$page_id = absint( get_option( $option ) );
	$page    = $page_id ? get_post( $page_id ) : null;

	if ( ! $page || $expected_slug !== $page->post_name ) {
		mr_audit_fail( $failures, sprintf( '%1$s is not wired to /%2$s/.', $option, $expected_slug ) );
	}
}

$expected_categories = array(
	'tinctures',
	'capsules',
	'vape-cartridges',
	'terpsolate-diamonds',
	'topicals',
	'body-lotion',
);

foreach ( $expected_categories as $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );

	if ( ! $term || is_wp_error( $term ) ) {
		mr_audit_fail( $failures, 'Missing product category: ' . $slug );
		continue;
	}

	if ( ! absint( get_term_meta( absint( $term->term_id ), 'thumbnail_id', true ) ) ) {
		mr_audit_fail( $failures, 'Product category has no thumbnail: ' . $slug );
	}
}

$products = get_posts(
	array(
		'post_type'        => 'product',
		'post_status'      => 'publish',
		'posts_per_page'   => -1,
		'orderby'          => 'ID',
		'order'            => 'ASC',
		'suppress_filters' => false,
	)
);

if ( count( $products ) < 30 ) {
	mr_audit_fail( $failures, 'Expected at least 30 published products; found ' . count( $products ) . '.' );
}

$required_product_meta = array(
	'_mr_ingredients'   => 'ingredients',
	'_mr_suggested_use' => 'suggested use',
	'_mr_warning'       => 'warning',
);

foreach ( $products as $product_post ) {
	$product_id = absint( $product_post->ID );
	$product    = wc_get_product( $product_id );
	$label      = sprintf( '%1$s (#%2$d)', get_the_title( $product_id ), $product_id );

	if ( ! $product ) {
		mr_audit_fail( $failures, 'Could not load WooCommerce product object for ' . $label . '.' );
		continue;
	}

	if ( '' === $product->get_sku() ) {
		mr_audit_fail( $failures, 'Published product has no SKU: ' . $label . '.' );
	}

	if ( ! $product->get_image_id() ) {
		mr_audit_fail( $failures, 'Published product has no primary image: ' . $label . '.' );
	}

	foreach ( $required_product_meta as $meta_key => $meta_label ) {
		if ( '' === mr_audit_meta( $product_id, $meta_key ) ) {
			mr_audit_fail( $failures, sprintf( '%1$s is missing %2$s meta.', $label, $meta_label ) );
		}
	}

	$coa_id = absint( get_post_meta( $product_id, '_mr_current_coa_id', true ) );

	if ( ! $coa_id ) {
		mr_audit_fail( $failures, 'Published product has no current COA: ' . $label . '.' );
		continue;
	}

	$coa = get_post( $coa_id );

	if ( ! $coa || 'mr_coa' !== $coa->post_type || 'publish' !== $coa->post_status ) {
		mr_audit_fail( $failures, 'Current COA is invalid for ' . $label . '.' );
		continue;
	}

	$related_ids = get_post_meta( $coa_id, '_mr_coa_related_product_ids', true );
	$related_ids = is_array( $related_ids ) ? array_map( 'absint', $related_ids ) : array_filter( array_map( 'absint', explode( ',', (string) $related_ids ) ) );

	if ( ! in_array( $product_id, $related_ids, true ) ) {
		mr_audit_fail( $failures, 'Current COA does not point back to product: ' . $label . '.' );
	}

	if ( 'current' !== mr_audit_meta( $coa_id, '_mr_coa_status' ) ) {
		mr_audit_fail( $failures, 'COA status is not current for ' . $label . '.' );
	}

	foreach ( array( '_mr_coa_batch_id', '_mr_coa_test_date', '_mr_coa_lab_name' ) as $coa_meta_key ) {
		if ( '' === mr_audit_meta( $coa_id, $coa_meta_key ) ) {
			mr_audit_fail( $failures, sprintf( '%1$s COA is missing %2$s.', $label, $coa_meta_key ) );
		}
	}

	$attachment_id = absint( get_post_meta( $coa_id, '_mr_coa_attachment_id', true ) );
	$file_path     = $attachment_id ? get_attached_file( $attachment_id ) : '';

	if ( ! $attachment_id || ! wp_get_attachment_url( $attachment_id ) || ! $file_path || ! file_exists( $file_path ) ) {
		mr_audit_fail( $failures, 'COA attachment is missing or unreadable for ' . $label . '.' );
	}
}

$coa_count = wp_count_posts( 'mr_coa' );

if ( ! $coa_count || absint( $coa_count->publish ?? 0 ) < count( $products ) ) {
	mr_audit_fail( $failures, 'Published COA count is lower than published product count.' );
}

$lab_results_html = do_shortcode( '[meraki_lab_results]' );

if ( false === strpos( $lab_results_html, 'meraki-lab-results' ) || false === strpos( $lab_results_html, 'View PDF' ) ) {
	mr_audit_fail( $failures, 'Lab results shortcode did not render usable COA output.' );
}

$deprecated_block_templates = array(
	'single-product.html',
	'archive-product.html',
	'taxonomy-product_cat.html',
);

foreach ( $deprecated_block_templates as $template_file ) {
	if ( file_exists( get_theme_file_path( 'templates/' . $template_file ) ) ) {
		mr_audit_fail( $failures, 'Deprecated WooCommerce block template still exists: templates/' . $template_file );
	}
}

$draft_products = get_posts(
	array(
		'post_type'        => 'product',
		'post_status'      => 'draft',
		'posts_per_page'   => -1,
		'suppress_filters' => false,
	)
);

foreach ( $draft_products as $draft_product ) {
	if ( ! get_post_thumbnail_id( $draft_product ) ) {
		mr_audit_warn( $warnings, 'Draft product has no primary image: ' . get_the_title( $draft_product ) . ' (#' . $draft_product->ID . ').' );
	}
}

if ( $warnings ) {
	foreach ( $warnings as $warning ) {
		WP_CLI::warning( $warning );
	}
}

if ( $failures ) {
	foreach ( $failures as $failure ) {
		WP_CLI::log( 'FAIL: ' . $failure );
	}

	WP_CLI::error( 'Meraki launch audit failed with ' . count( $failures ) . ' issue(s).' );
}

WP_CLI::success(
	sprintf(
		'Meraki launch audit passed: %1$d published products, %2$d published COAs, %3$d warning(s).',
		count( $products ),
		absint( $coa_count->publish ?? 0 ),
		count( $warnings )
	)
);
