<?php
/**
 * COA admin metabox.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\COA;

defined( 'ABSPATH' ) || exit;

/**
 * Renders and saves COA admin fields.
 */
class CoaAdminMetaBox {
	/**
	 * Register the metabox.
	 *
	 * @return void
	 */
	public function register() {
		add_meta_box(
			'meraki-commerce-core-coa',
			__( 'COA Details', 'meraki-commerce-core' ),
			array( $this, 'render' ),
			CoaPostType::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render field markup.
	 *
	 * @param \WP_Post $post Current COA post.
	 * @return void
	 */
	public function render( $post ) {
		wp_nonce_field( 'meraki_commerce_core_save_coa', 'meraki_commerce_core_coa_nonce' );

		$attachment_id = absint( get_post_meta( $post->ID, '_mr_coa_attachment_id', true ) );
		$batch_id      = (string) get_post_meta( $post->ID, '_mr_coa_batch_id', true );
		$test_date     = (string) get_post_meta( $post->ID, '_mr_coa_test_date', true );
		$lab_name      = (string) get_post_meta( $post->ID, '_mr_coa_lab_name', true );
		$product_ids   = CoaNormalizer::normalize_product_ids( get_post_meta( $post->ID, '_mr_coa_related_product_ids', true ) );
		$status        = (string) get_post_meta( $post->ID, '_mr_coa_status', true );
		$legacy_url    = (string) get_post_meta( $post->ID, '_mr_coa_legacy_url', true );
		$pdf_url       = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
		?>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="mr_coa_attachment_id"><?php esc_html_e( 'COA Attachment ID', 'meraki-commerce-core' ); ?></label></th>
					<td>
						<input type="number" class="regular-text" id="mr_coa_attachment_id" name="mr_coa_attachment_id" value="<?php echo esc_attr( (string) $attachment_id ); ?>" />
						<?php if ( $pdf_url ) : ?>
							<p><a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open current PDF', 'meraki-commerce-core' ); ?></a></p>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="mr_coa_batch_id"><?php esc_html_e( 'Batch ID', 'meraki-commerce-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="mr_coa_batch_id" name="mr_coa_batch_id" value="<?php echo esc_attr( $batch_id ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="mr_coa_test_date"><?php esc_html_e( 'Test Date', 'meraki-commerce-core' ); ?></label></th>
					<td><input type="date" class="regular-text" id="mr_coa_test_date" name="mr_coa_test_date" value="<?php echo esc_attr( $test_date ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="mr_coa_lab_name"><?php esc_html_e( 'Lab Name', 'meraki-commerce-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="mr_coa_lab_name" name="mr_coa_lab_name" value="<?php echo esc_attr( $lab_name ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="mr_coa_related_product_ids"><?php esc_html_e( 'Related Product IDs', 'meraki-commerce-core' ); ?></label></th>
					<td><input type="text" class="regular-text" id="mr_coa_related_product_ids" name="mr_coa_related_product_ids" value="<?php echo esc_attr( implode( ',', $product_ids ) ); ?>" /><p class="description"><?php esc_html_e( 'Comma-separated product IDs related to this COA.', 'meraki-commerce-core' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="mr_coa_status"><?php esc_html_e( 'Status', 'meraki-commerce-core' ); ?></label></th>
					<td>
						<select id="mr_coa_status" name="mr_coa_status">
							<?php foreach ( array( 'current', 'archived', 'superseded' ) as $option ) : ?>
								<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $status ? $status : 'current', $option ); ?>><?php echo esc_html( ucfirst( $option ) ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="mr_coa_legacy_url"><?php esc_html_e( 'Legacy COA URL', 'meraki-commerce-core' ); ?></label></th>
					<td><input type="url" class="large-text" id="mr_coa_legacy_url" name="mr_coa_legacy_url" value="<?php echo esc_attr( $legacy_url ); ?>" /></td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Save metabox values.
	 *
	 * @param int      $post_id COA ID.
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function save( $post_id, $post ) {
		if ( ! isset( $_POST['meraki_commerce_core_coa_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['meraki_commerce_core_coa_nonce'] ) ), 'meraki_commerce_core_save_coa' ) ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( CoaPostType::POST_TYPE !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$test_date   = isset( $_POST['mr_coa_test_date'] ) ? sanitize_text_field( wp_unslash( $_POST['mr_coa_test_date'] ) ) : '';
		$product_ids = isset( $_POST['mr_coa_related_product_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['mr_coa_related_product_ids'] ) ) : '';
		$status      = isset( $_POST['mr_coa_status'] ) ? sanitize_key( wp_unslash( $_POST['mr_coa_status'] ) ) : 'current';
		$legacy_url  = isset( $_POST['mr_coa_legacy_url'] ) ? esc_url_raw( wp_unslash( $_POST['mr_coa_legacy_url'] ) ) : '';

		update_post_meta( $post_id, '_mr_coa_attachment_id', isset( $_POST['mr_coa_attachment_id'] ) ? absint( wp_unslash( $_POST['mr_coa_attachment_id'] ) ) : 0 );
		update_post_meta( $post_id, '_mr_coa_batch_id', isset( $_POST['mr_coa_batch_id'] ) ? sanitize_text_field( wp_unslash( $_POST['mr_coa_batch_id'] ) ) : '' );
		update_post_meta( $post_id, '_mr_coa_test_date', CoaNormalizer::normalize_date( $test_date ) );
		update_post_meta( $post_id, '_mr_coa_lab_name', isset( $_POST['mr_coa_lab_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mr_coa_lab_name'] ) ) : '' );
		update_post_meta( $post_id, '_mr_coa_related_product_ids', CoaNormalizer::normalize_product_ids( $product_ids ) );
		update_post_meta( $post_id, '_mr_coa_status', CoaNormalizer::normalize_status( $status ) );
		update_post_meta( $post_id, '_mr_coa_legacy_url', CoaNormalizer::normalize_url( $legacy_url ) );
	}
}
