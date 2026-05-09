<?php
/**
 * WP-CLI migration command.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\COA;

defined( 'ABSPATH' ) || exit;

/**
 * Migrates legacy COA URLs into normalized COA posts.
 */
class CoaMigrationCommand {
	/**
	 * Repository.
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
	 * Register the WP-CLI command.
	 *
	 * @param CoaRepository $repository Repository instance.
	 * @return void
	 */
	public static function register( CoaRepository $repository ) {
		$instance = new self( $repository );
		\WP_CLI::add_command( 'meraki coa migrate-legacy', array( $instance, 'migrate_legacy' ) );
	}

	/**
	 * Migrate legacy `_mr_coa_file` URLs.
	 *
	 * ## OPTIONS
	 *
	 * [--product_ids=<ids>]
	 * : Optional comma-separated list of product IDs.
	 *
	 * [--dry-run]
	 * : Report changes without writing.
	 *
	 * [--create-missing-attachments]
	 * : Attempt to sideload matching URLs into the Media Library when an attachment cannot be found.
	 *
	 * [--force-relink]
	 * : Overwrite an existing `_mr_current_coa_id`.
	 *
	 * @param array $args Positional arguments.
	 * @param array $assoc_args Associative arguments.
	 * @return void
	 */
	public function migrate_legacy( $args, $assoc_args ) {
		$product_ids                 = isset( $assoc_args['product_ids'] ) ? CoaNormalizer::normalize_product_ids( $assoc_args['product_ids'] ) : array();
		$dry_run                    = \WP_CLI\Utils\get_flag_value( $assoc_args, 'dry-run', false );
		$create_missing_attachments = \WP_CLI\Utils\get_flag_value( $assoc_args, 'create-missing-attachments', false );
		$force_relink               = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force-relink', false );

		$query = array(
			'post_type'      => 'product',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_mr_coa_file',
					'compare' => 'EXISTS',
				),
			),
		);

		if ( ! empty( $product_ids ) ) {
			$query['post__in'] = $product_ids;
		}

		$products = get_posts( $query );

		if ( empty( $products ) ) {
			\WP_CLI::success( __( 'No products with legacy COA URLs were found.', 'meraki-commerce-core' ) );
			return;
		}

		foreach ( $products as $product_id ) {
			$legacy_url = CoaNormalizer::normalize_url( (string) get_post_meta( $product_id, '_mr_coa_file', true ) );

			if ( '' === $legacy_url ) {
				\WP_CLI::warning( sprintf( 'Skipping product %d because the legacy COA URL is empty.', $product_id ) );
				continue;
			}

			$attachment_id = $this->find_attachment_id_for_url( $legacy_url );

			if ( ! $attachment_id && $create_missing_attachments && ! $dry_run ) {
				$attachment_id = $this->import_attachment_from_url( $legacy_url, $product_id );
			}

			$record = array(
				'_mr_coa_attachment_id' => $attachment_id,
				'_mr_coa_legacy_url'    => $legacy_url,
				'_mr_coa_status'        => 'current',
			);

			if ( $dry_run ) {
				\WP_CLI::log( sprintf( 'Would migrate product %d to a COA record using URL %s', $product_id, $legacy_url ) );
				continue;
			}

			$coa_id = $this->repository->upsert_for_product( $product_id, $record, $force_relink );

			if ( ! $coa_id ) {
				\WP_CLI::warning( sprintf( 'Could not create a COA record for product %d.', $product_id ) );
				continue;
			}

			\WP_CLI::success( sprintf( 'Product %1$d linked to COA %2$d.', $product_id, $coa_id ) );
		}
	}

	/**
	 * Resolve an attachment ID from a URL.
	 *
	 * @param string $url File URL.
	 * @return int
	 */
	private function find_attachment_id_for_url( $url ) {
		$attachment_id = attachment_url_to_postid( $url );

		if ( $attachment_id ) {
			return absint( $attachment_id );
		}

		global $wpdb;
		$like = '%' . $wpdb->esc_like( wp_basename( $url ) ) . '%';

		return absint(
			$wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND guid LIKE %s ORDER BY ID DESC LIMIT 1",
					$like
				)
			)
		);
	}

	/**
	 * Import a remote or local attachment into the Media Library.
	 *
	 * @param string $url File URL.
	 * @param int    $product_id Product ID.
	 * @return int
	 */
	private function import_attachment_from_url( $url, $product_id ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_sideload_image( $url, $product_id, null, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			\WP_CLI::warning( sprintf( 'Could not sideload %1$s: %2$s', $url, $attachment_id->get_error_message() ) );
			return 0;
		}

		return absint( $attachment_id );
	}
}
