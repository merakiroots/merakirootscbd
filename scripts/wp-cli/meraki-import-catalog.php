<?php
/**
 * Import Meraki Roots launch catalog, local product media, category media, and COA records.
 *
 * Run with:
 * wp eval-file scripts/wp-cli/meraki-import-catalog.php
 *
 * @package MerakiRootsLaunch
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with: wp eval-file scripts/wp-cli/meraki-import-catalog.php\n" );
	exit( 1 );
}

if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product_Simple' ) ) {
	WP_CLI::error( 'WooCommerce must be active before importing the Meraki catalog.' );
}

/**
 * Read a CSV into associative rows.
 *
 * @param string $path CSV path.
 * @return array<int,array<string,string>>
 */
function mr_import_read_csv( string $path ): array {
	if ( ! is_readable( $path ) ) {
		WP_CLI::error( 'CSV not readable: ' . $path );
	}

	$handle = fopen( $path, 'r' );
	if ( false === $handle ) {
		WP_CLI::error( 'Could not open CSV: ' . $path );
	}

	$headers = fgetcsv( $handle );
	if ( ! is_array( $headers ) ) {
		fclose( $handle );
		WP_CLI::error( 'CSV has no header row: ' . $path );
	}

	$rows = array();

	while ( false !== ( $data = fgetcsv( $handle ) ) ) {
		if ( array( null ) === $data || array() === $data ) {
			continue;
		}

		$row = array();
		foreach ( $headers as $index => $header ) {
			$row[ (string) $header ] = isset( $data[ $index ] ) ? (string) $data[ $index ] : '';
		}

		$rows[] = $row;
	}

	fclose( $handle );

	return $rows;
}

/**
 * Truthy CSV value helper.
 *
 * @param string $value Raw value.
 * @return bool
 */
function mr_import_truthy( string $value ): bool {
	return in_array( strtolower( trim( $value ) ), array( '1', 'true', 'yes', 'y' ), true );
}

/**
 * Split a comma-separated field.
 *
 * @param string $value Raw value.
 * @return array<int,string>
 */
function mr_import_split_list( string $value ): array {
	if ( '' === trim( $value ) ) {
		return array();
	}

	return array_values(
		array_filter(
			array_map(
				static function ( string $item ): string {
					return trim( $item );
				},
				preg_split( '/\s*(?:,|\|)\s*/', $value ) ?: array()
			),
			static function ( string $item ): bool {
				return '' !== $item;
			}
		)
	);
}

/**
 * Get product-photo source roots in preference order.
 *
 * @return array<int,string>
 */
function mr_import_product_photo_roots(): array {
	return array(
		wp_normalize_path( WP_CONTENT_DIR . '/uploads/product-photos-current' ),
		wp_normalize_path( WP_CONTENT_DIR . '/uploads/product-photos-expanded' ),
	);
}

/**
 * Resolve a product-photo path from a local product photo pack.
 *
 * @param string $relative_path Path from mapping CSV.
 * @return string
 */
function mr_import_resolve_product_photo( string $relative_path ): string {
	$relative_path = trim( str_replace( '\\', '/', $relative_path ) );

	if ( '' === $relative_path ) {
		return '';
	}

	foreach ( mr_import_product_photo_roots() as $root ) {
		$path = wp_normalize_path( $root . '/' . $relative_path );

		if ( ! file_exists( $path ) ) {
			continue;
		}

		$real_root = realpath( $root );
		$real_path = realpath( $path );

		if ( false === $real_root || false === $real_path ) {
			continue;
		}

		$real_root = wp_normalize_path( $real_root );
		$real_path = wp_normalize_path( $real_path );

		if ( 0 === strpos( $real_path, trailingslashit( $real_root ) ) ) {
			return $real_path;
		}
	}

	return '';
}

/**
 * Find an existing attachment by its uploads-relative file path.
 *
 * @param string $relative_path Uploads-relative path.
 * @return int
 */
function mr_import_find_attachment_by_relative_path( string $relative_path ): int {
	$matches = get_posts(
		array(
			'post_type'        => 'attachment',
			'post_status'      => 'inherit',
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'meta_key'         => '_wp_attached_file',
			'meta_value'       => $relative_path,
			'suppress_filters' => true,
		)
	);

	return empty( $matches ) ? 0 : absint( $matches[0] );
}

/**
 * Register a file already located inside wp-content/uploads as an attachment.
 *
 * @param string $absolute_path Absolute file path.
 * @param string $alt_text Alt text.
 * @return int
 */
function mr_import_attachment_from_upload_file( string $absolute_path, string $alt_text = '' ): int {
	$absolute_path = wp_normalize_path( $absolute_path );
	$upload_dir    = wp_upload_dir();
	$upload_base   = wp_normalize_path( $upload_dir['basedir'] );

	if ( ! file_exists( $absolute_path ) || 0 !== strpos( $absolute_path, trailingslashit( $upload_base ) ) ) {
		return 0;
	}

	$relative_path = ltrim( substr( $absolute_path, strlen( $upload_base ) ), '/' );
	$attachment_id = mr_import_find_attachment_by_relative_path( $relative_path );

	if ( $attachment_id ) {
		if ( '' !== $alt_text ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
		}
		return $attachment_id;
	}

	$filetype = wp_check_filetype( $absolute_path );
	$title    = sanitize_text_field( pathinfo( $absolute_path, PATHINFO_FILENAME ) );
	$url_path = str_replace( '%2F', '/', rawurlencode( $relative_path ) );

	$attachment_id = wp_insert_attachment(
		array(
			'guid'           => trailingslashit( $upload_dir['baseurl'] ) . $url_path,
			'post_mime_type' => $filetype['type'] ?: 'application/octet-stream',
			'post_title'     => $title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$absolute_path
	);

	if ( is_wp_error( $attachment_id ) ) {
		WP_CLI::warning( 'Could not create attachment for ' . $absolute_path . ': ' . $attachment_id->get_error_message() );
		return 0;
	}

	update_post_meta( $attachment_id, '_wp_attached_file', $relative_path );

	if ( '' !== $alt_text ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $alt_text ) );
	}

	if ( 0 === strpos( (string) $filetype['type'], 'image/' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$metadata = wp_generate_attachment_metadata( $attachment_id, $absolute_path );
		if ( is_array( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}
	}

	return absint( $attachment_id );
}

/**
 * Load product image mappings keyed by SKU.
 *
 * @param string $csv_path Mapping CSV.
 * @return array<string,array<string,string>>
 */
function mr_import_product_image_map( string $csv_path ): array {
	$map = array();

	foreach ( mr_import_read_csv( $csv_path ) as $row ) {
		$sku = trim( $row['SKU'] ?? '' );
		if ( '' !== $sku ) {
			$map[ $sku ] = $row;
		}
	}

	return $map;
}

/**
 * Import product images for a SKU and return attachment IDs.
 *
 * @param string                    $sku Product SKU.
 * @param array<string,string>|null $image_row Mapping row.
 * @param string                    $fallback_alt Fallback alt.
 * @return array{primary:int,gallery:array<int,int>}
 */
function mr_import_product_images( string $sku, ?array $image_row, string $fallback_alt ): array {
	if ( null === $image_row ) {
		return array(
			'primary' => 0,
			'gallery' => array(),
		);
	}

	$alt        = trim( $image_row['Suggested Alt Text'] ?? '' );
	$alt        = '' === $alt ? $fallback_alt : $alt;
	$primary_id = 0;
	$gallery    = array();

	$primary_path = mr_import_resolve_product_photo( $image_row['Primary Local Image'] ?? '' );
	if ( '' !== $primary_path ) {
		$primary_id = mr_import_attachment_from_upload_file( $primary_path, $alt );
	} else {
		WP_CLI::warning( sprintf( 'Product %s has no readable primary local image.', $sku ) );
	}

	foreach ( mr_import_split_list( $image_row['Gallery Local Images'] ?? '' ) as $gallery_path ) {
		$absolute_path = mr_import_resolve_product_photo( $gallery_path );
		if ( '' === $absolute_path ) {
			WP_CLI::warning( sprintf( 'Product %s has unreadable gallery image: %s', $sku, $gallery_path ) );
			continue;
		}

		$attachment_id = mr_import_attachment_from_upload_file( $absolute_path, $alt );
		if ( $attachment_id ) {
			$gallery[] = $attachment_id;
		}
	}

	return array(
		'primary' => $primary_id,
		'gallery' => array_values( array_unique( array_filter( $gallery ) ) ),
	);
}

/**
 * Ensure a product category path exists and return term IDs for all path levels.
 *
 * @param string $category_path Category path, e.g. Topicals > Body Lotion.
 * @return array<int,int>
 */
function mr_import_ensure_product_category_path( string $category_path ): array {
	$parent_id = 0;
	$term_ids  = array();

	foreach ( array_map( 'trim', explode( '>', $category_path ) ) as $name ) {
		if ( '' === $name ) {
			continue;
		}

		$term = get_term_by( 'name', $name, 'product_cat' );

		if ( ! $term || is_wp_error( $term ) ) {
			$created = wp_insert_term(
				$name,
				'product_cat',
				array(
					'slug'   => sanitize_title( $name ),
					'parent' => $parent_id,
				)
			);

			if ( is_wp_error( $created ) ) {
				WP_CLI::warning( 'Could not create product category ' . $name . ': ' . $created->get_error_message() );
				continue;
			}

			$term_id = absint( $created['term_id'] );
		} else {
			$term_id = absint( $term->term_id );
			if ( $parent_id && absint( $term->parent ) !== $parent_id ) {
				wp_update_term( $term_id, 'product_cat', array( 'parent' => $parent_id ) );
			}
		}

		$parent_id  = $term_id;
		$term_ids[] = $term_id;
	}

	return $term_ids;
}

/**
 * Get product category IDs from CSV value.
 *
 * @param string $categories Raw categories.
 * @return array<int,int>
 */
function mr_import_product_category_ids( string $categories ): array {
	$ids = array();

	foreach ( mr_import_split_list( $categories ) as $category_path ) {
		$ids = array_merge( $ids, mr_import_ensure_product_category_path( $category_path ) );
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Ensure product tags exist and return IDs.
 *
 * @param string $tags Raw tag list.
 * @return array<int,int>
 */
function mr_import_product_tag_ids( string $tags ): array {
	$ids = array();

	foreach ( mr_import_split_list( $tags ) as $tag_name ) {
		$term = get_term_by( 'name', $tag_name, 'product_tag' );

		if ( ! $term || is_wp_error( $term ) ) {
			$created = wp_insert_term( $tag_name, 'product_tag', array( 'slug' => sanitize_title( $tag_name ) ) );

			if ( is_wp_error( $created ) ) {
				WP_CLI::warning( 'Could not create product tag ' . $tag_name . ': ' . $created->get_error_message() );
				continue;
			}

			$ids[] = absint( $created['term_id'] );
		} else {
			$ids[] = absint( $term->term_id );
		}
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Import product category thumbnail mappings.
 *
 * @param string $csv_path Mapping CSV.
 * @return int
 */
function mr_import_category_media( string $csv_path ): int {
	$count = 0;

	foreach ( mr_import_read_csv( $csv_path ) as $row ) {
		$slug = trim( $row['Slug'] ?? '' );
		if ( '' === $slug ) {
			continue;
		}

		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}

		$description = trim( $row['Safe Category Description'] ?? '' );
		if ( '' !== $description ) {
			wp_update_term( absint( $term->term_id ), 'product_cat', array( 'description' => $description ) );
		}

		$image_path = mr_import_resolve_product_photo( $row['Local Hero/Thumbnail Image'] ?? '' );
		if ( '' === $image_path ) {
			WP_CLI::warning( sprintf( 'Category %s has no readable local image.', $slug ) );
			continue;
		}

		$attachment_id = mr_import_attachment_from_upload_file( $image_path, (string) ( $row['Category'] ?? '' ) );
		if ( $attachment_id ) {
			update_term_meta( absint( $term->term_id ), 'thumbnail_id', $attachment_id );
			++$count;
		}
	}

	return $count;
}

/**
 * Resolve and copy a COA PDF into uploads if needed.
 *
 * @param string $coa_url COA URL/path from CSV.
 * @return int
 */
function mr_import_coa_attachment( string $coa_url ): int {
	$coa_url = trim( $coa_url );
	if ( '' === $coa_url ) {
		return 0;
	}

	$path = (string) wp_parse_url( $coa_url, PHP_URL_PATH );
	if ( '' === $path ) {
		$path = $coa_url;
	}

	$path = wp_normalize_path( $path );
	if ( 0 !== strpos( $path, '/wp-content/uploads/' ) ) {
		return 0;
	}

	$relative_upload = ltrim( substr( $path, strlen( '/wp-content/uploads/' ) ), '/' );
	$upload_dir      = wp_upload_dir();
	$dest_path       = wp_normalize_path( trailingslashit( $upload_dir['basedir'] ) . $relative_upload );

	$relative_lab_path = preg_replace( '#^lab-results/#', '', $relative_upload );
	$source_paths      = array();

	if ( defined( 'MERAKI_COMMERCE_CORE_DIR' ) && $relative_lab_path ) {
		$source_paths[] = wp_normalize_path( MERAKI_COMMERCE_CORE_DIR . 'uploads/lab-results/' . $relative_lab_path );
		$source_paths[] = wp_normalize_path( MERAKI_COMMERCE_CORE_DIR . 'assets/blocks/lab-results/lab-results/' . $relative_lab_path );
	}

	$source_path = '';
	foreach ( $source_paths as $candidate_path ) {
		if ( file_exists( $candidate_path ) ) {
			$source_path = $candidate_path;
			break;
		}
	}

	if ( '' !== $source_path && ( ! file_exists( $dest_path ) || hash_file( 'sha256', $source_path ) !== hash_file( 'sha256', $dest_path ) ) ) {
		wp_mkdir_p( dirname( $dest_path ) );
		if ( ! copy( $source_path, $dest_path ) ) {
			WP_CLI::warning( 'Could not copy COA PDF to uploads: ' . $dest_path );
			return 0;
		}
	} elseif ( ! file_exists( $dest_path ) ) {
		WP_CLI::warning( 'No local COA PDF found for ' . $coa_url );
		return 0;
	}

	return mr_import_attachment_from_upload_file( $dest_path, pathinfo( $dest_path, PATHINFO_FILENAME ) );
}

/**
 * Create or update the COA post for a product row.
 *
 * @param int                  $product_id Product ID.
 * @param array<string,string> $row Product CSV row.
 * @return int
 */
function mr_import_upsert_product_coa( int $product_id, array $row ): int {
	$coa_url = trim( $row['meta:_mr_coa_file'] ?? '' );
	if ( '' === $coa_url ) {
		return 0;
	}

	$attachment_id = mr_import_coa_attachment( $coa_url );
	$coa_id        = absint( get_post_meta( $product_id, '_mr_current_coa_id', true ) );

	if ( $coa_id ) {
		$existing = get_post( $coa_id );
		if ( ! $existing || 'mr_coa' !== $existing->post_type ) {
			$coa_id = 0;
		}
	}

	if ( ! $coa_id ) {
		$coa_id = wp_insert_post(
			array(
				'post_type'   => 'mr_coa',
				'post_status' => 'publish',
				'post_title'  => sprintf( '%s COA', get_the_title( $product_id ) ),
			),
			true
		);

		if ( is_wp_error( $coa_id ) ) {
			WP_CLI::warning( sprintf( 'Could not create COA for product %d: %s', $product_id, $coa_id->get_error_message() ) );
			return 0;
		}
	}

	update_post_meta( $coa_id, '_mr_coa_attachment_id', $attachment_id );
	update_post_meta( $coa_id, '_mr_coa_legacy_url', esc_url_raw( $coa_url ) );
	update_post_meta( $coa_id, '_mr_coa_batch_id', sanitize_text_field( $row['meta:_mr_coa_batch_id'] ?? '' ) );
	update_post_meta( $coa_id, '_mr_coa_test_date', sanitize_text_field( $row['meta:_mr_coa_test_date'] ?? '' ) );
	update_post_meta( $coa_id, '_mr_coa_lab_name', sanitize_text_field( $row['meta:_mr_coa_lab_name'] ?? '' ) );
	update_post_meta( $coa_id, '_mr_coa_status', 'current' );
	update_post_meta( $coa_id, '_mr_coa_related_product_ids', array( $product_id ) );
	update_post_meta( $product_id, '_mr_current_coa_id', absint( $coa_id ) );

	return absint( $coa_id );
}

/**
 * Build local product attributes from CSV columns.
 *
 * @param array<string,string> $row Product CSV row.
 * @return array<int,\WC_Product_Attribute>
 */
function mr_import_product_attributes( array $row ): array {
	$attributes = array();

	for ( $index = 1; $index <= 8; ++$index ) {
		$name  = trim( $row[ 'Attribute ' . $index . ' name' ] ?? '' );
		$value = trim( $row[ 'Attribute ' . $index . ' value(s)' ] ?? '' );

		if ( '' === $name || '' === $value ) {
			continue;
		}

		$attribute = new WC_Product_Attribute();
		$attribute->set_id( 0 );
		$attribute->set_name( $name );
		$attribute->set_options( array_map( 'trim', explode( '|', $value ) ) );
		$attribute->set_visible( mr_import_truthy( $row[ 'Attribute ' . $index . ' visible' ] ?? '0' ) );
		$attribute->set_variation( false );

		$attributes[] = $attribute;
	}

	return $attributes;
}

/**
 * Upsert a WooCommerce simple product from a CSV row.
 *
 * @param array<string,string>            $row Product CSV row.
 * @param array<string,array<string,string>> $image_map Product image map.
 * @return int
 */
function mr_import_upsert_product( array $row, array $image_map ): int {
	$sku = trim( $row['SKU'] ?? '' );
	if ( '' === $sku ) {
		WP_CLI::warning( 'Skipping product row with no SKU.' );
		return 0;
	}

	$product_id = wc_get_product_id_by_sku( $sku );
	$product    = $product_id ? wc_get_product( $product_id ) : new WC_Product_Simple();

	if ( ! $product instanceof WC_Product_Simple ) {
		WP_CLI::warning( sprintf( 'Skipping SKU %s because the existing product is not simple.', $sku ) );
		return 0;
	}

	$product->set_sku( $sku );
	$product->set_name( trim( $row['Name'] ?? $sku ) );
	$product->set_slug( sanitize_title( $row['Slug'] ?? '' ) );
	$product->set_status( mr_import_truthy( $row['Published'] ?? '0' ) ? 'publish' : 'draft' );
	$product->set_catalog_visibility( trim( $row['Visibility in catalog'] ?? 'visible' ) ?: 'visible' );
	$product->set_short_description( wp_kses_post( $row['Short description'] ?? '' ) );
	$product->set_description( wp_kses_post( $row['Description'] ?? '' ) );
	$product->set_regular_price( trim( $row['Regular price'] ?? '' ) );
	$product->set_sale_price( trim( $row['Sale price'] ?? '' ) );
	$product->set_tax_status( trim( $row['Tax status'] ?? 'taxable' ) ?: 'taxable' );
	$product->set_tax_class( trim( $row['Tax class'] ?? '' ) );
	$product->set_featured( mr_import_truthy( $row['Is featured?'] ?? '0' ) );
	$product->set_reviews_allowed( mr_import_truthy( $row['Allow customer reviews?'] ?? '1' ) );
	$product->set_purchase_note( wp_kses_post( $row['Purchase note'] ?? '' ) );
	$product->set_menu_order( absint( $row['Position'] ?? 0 ) );
	$product->set_sold_individually( mr_import_truthy( $row['Sold individually?'] ?? '0' ) );
	$product->set_backorders( mr_import_truthy( $row['Backorders allowed?'] ?? '0' ) ? 'yes' : 'no' );
	$product->set_stock_status( mr_import_truthy( $row['In stock?'] ?? '1' ) ? 'instock' : 'outofstock' );

	if ( '' !== trim( $row['Stock'] ?? '' ) ) {
		$product->set_manage_stock( true );
		$product->set_stock_quantity( wc_stock_amount( $row['Stock'] ) );
	}

	$product->set_weight( trim( $row['Weight (lbs)'] ?? '' ) );
	$product->set_length( trim( $row['Length (in)'] ?? '' ) );
	$product->set_width( trim( $row['Width (in)'] ?? '' ) );
	$product->set_height( trim( $row['Height (in)'] ?? '' ) );
	$product->set_category_ids( mr_import_product_category_ids( $row['Categories'] ?? '' ) );
	$product->set_tag_ids( mr_import_product_tag_ids( $row['Tags'] ?? '' ) );
	$product->set_attributes( mr_import_product_attributes( $row ) );

	$images = mr_import_product_images( $sku, $image_map[ $sku ] ?? null, (string) ( $row['Name'] ?? $sku ) );
	if ( $images['primary'] ) {
		$product->set_image_id( $images['primary'] );
	}
	if ( ! empty( $images['gallery'] ) ) {
		$product->set_gallery_image_ids( $images['gallery'] );
	}

	$product_id = $product->save();

	foreach ( $row as $column => $value ) {
		if ( 0 === strpos( $column, 'meta:' ) ) {
			$meta_key = substr( $column, 5 );
			if ( '' !== $meta_key ) {
				update_post_meta( $product_id, $meta_key, wp_kses_post( $value ) );
			}
		}
	}

	mr_import_upsert_product_coa( absint( $product_id ), $row );
	wc_delete_product_transients( absint( $product_id ) );

	return absint( $product_id );
}

$import_dir        = WP_CONTENT_DIR . '/plugins/meraki-commerce-core/uploads/imports';
$product_csv       = $import_dir . '/meraki_woocommerce_products_full_import_v3.csv';
$image_map_csv     = $import_dir . '/meraki_product_image_map.csv';
$category_map_csv  = $import_dir . '/meraki_category_media_map.csv';
$image_map         = mr_import_product_image_map( $image_map_csv );
$products_imported = 0;
$coas_linked       = 0;

foreach ( mr_import_read_csv( $product_csv ) as $row ) {
	$product_id = mr_import_upsert_product( $row, $image_map );
	if ( $product_id ) {
		++$products_imported;
		if ( absint( get_post_meta( $product_id, '_mr_current_coa_id', true ) ) ) {
			++$coas_linked;
		}
		WP_CLI::log( sprintf( 'Imported SKU %s as product %d.', $row['SKU'] ?? '', $product_id ) );
	}
}

$category_media_count = mr_import_category_media( $category_map_csv );

wc_delete_product_transients();
flush_rewrite_rules();

WP_CLI::success(
	sprintf(
		'Imported %1$d products, linked %2$d current COAs, and mapped %3$d category images.',
		$products_imported,
		$coas_linked,
		$category_media_count
	)
);
