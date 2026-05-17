<?php
/**
 * Product COA frontend presenter.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\Frontend;

use MerakiCommerceCore\Domain\COA\CoaRepository;
use MerakiCommerceCore\Domain\Compliance\ComplianceText;

defined( 'ABSPATH' ) || exit;

/**
 * Renders product-level trust and COA information.
 */
class ProductCoaPresenter {
	/**
	 * Repository.
	 *
	 * @var CoaRepository
	 */
	private $repository;

	/**
	 * Compliance strings.
	 *
	 * @var ComplianceText
	 */
	private $compliance_text;

	/**
	 * Constructor.
	 *
	 * @param CoaRepository  $repository Repository instance.
	 * @param ComplianceText $compliance_text Compliance text helper.
	 */
	public function __construct( CoaRepository $repository, ComplianceText $compliance_text ) {
		$this->repository      = $repository;
		$this->compliance_text = $compliance_text;
	}

	/**
	 * Render the COA summary beneath the buy box.
	 *
	 * @return void
	 */
	public function render_single_product_callout() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		global $product;

		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$coa = $this->repository->get_current_for_product( $product->get_id() );

		if ( ! $coa ) {
			return;
		}

		$data = $this->repository->get_public_data( $coa->ID );

		if ( ! $data ) {
			return;
		}

		$ingredients   = (string) get_post_meta( $product->get_id(), '_mr_ingredients', true );
		$suggested_use = (string) get_post_meta( $product->get_id(), '_mr_suggested_use', true );
		$warning       = (string) get_post_meta( $product->get_id(), '_mr_warning', true );
		$trust_badges_asset = WP_CONTENT_DIR . '/uploads/ui-assets/product-trust-icons.png';
		$trust_badges_url   = content_url( 'uploads/ui-assets/product-trust-icons.png' );
		$trust_badges       = array(
			array( 'THC', __( 'Free', 'meraki-commerce-core' ) ),
			array( 'Made in', __( 'USA', 'meraki-commerce-core' ) ),
			array( 'Natural', __( 'Ingredients', 'meraki-commerce-core' ) ),
			array( 'Non', __( 'GMO', 'meraki-commerce-core' ) ),
			array( '3rd Party', __( 'Lab Tested', 'meraki-commerce-core' ) ),
			array( 'Organic', __( 'Hemp', 'meraki-commerce-core' ) ),
		);
		?>
		<section class="meraki-product-coa" aria-label="<?php esc_attr_e( 'Lab result and trust details', 'meraki-commerce-core' ); ?>">
			<div class="mcc-trust-badges" aria-label="<?php esc_attr_e( 'Product trust signals', 'meraki-commerce-core' ); ?>">
				<?php if ( file_exists( $trust_badges_asset ) ) : ?>
					<img class="mcc-trust-badges__image" src="<?php echo esc_url( $trust_badges_url ); ?>" alt="<?php esc_attr_e( 'THC free, made in USA, natural ingredients, non GMO, third-party lab tested, made with organic hemp', 'meraki-commerce-core' ); ?>">
				<?php else : ?>
					<?php foreach ( $trust_badges as $badge ) : ?>
						<span class="mcc-trust-badge">
							<span class="mcc-trust-badge__mark" aria-hidden="true"></span>
							<span class="mcc-trust-badge__text">
								<span><?php echo esc_html( $badge[0] ); ?></span>
								<span><?php echo esc_html( $badge[1] ); ?></span>
							</span>
						</span>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<div class="mcc-product-accordions">
				<?php if ( $ingredients ) : ?>
					<details class="mcc-accordion" open>
						<summary class="mcc-accordion__summary"><?php esc_html_e( 'Ingredients', 'meraki-commerce-core' ); ?></summary>
						<div class="mcc-accordion__content"><?php echo wp_kses_post( wpautop( $ingredients ) ); ?></div>
					</details>
				<?php endif; ?>
				<?php if ( $suggested_use ) : ?>
					<details class="mcc-accordion">
						<summary class="mcc-accordion__summary"><?php esc_html_e( 'Suggested Use', 'meraki-commerce-core' ); ?></summary>
						<div class="mcc-accordion__content"><?php echo wp_kses_post( wpautop( $suggested_use ) ); ?></div>
					</details>
				<?php endif; ?>
				<?php if ( $warning ) : ?>
					<details class="mcc-accordion">
						<summary class="mcc-accordion__summary"><?php esc_html_e( 'Warning', 'meraki-commerce-core' ); ?></summary>
						<div class="mcc-accordion__content">
							<?php echo wp_kses_post( wpautop( $warning ) ); ?>
							<p class="meraki-product-coa__disclaimer"><?php echo esc_html( $this->compliance_text->get_fda_disclaimer() ); ?></p>
						</div>
					</details>
				<?php endif; ?>
				<?php if ( $data['attachment_url'] ) : ?>
					<details class="mcc-accordion">
						<summary class="mcc-accordion__summary"><?php esc_html_e( 'Lab Results', 'meraki-commerce-core' ); ?></summary>
						<div class="mcc-accordion__content">
							<?php if ( $data['lab_name'] || $data['test_date'] || $data['batch_id'] ) : ?>
								<p class="mcc-coa-meta-summary">
									<?php echo esc_html( trim( implode( ' | ', array_filter( array( $data['lab_name'], $data['test_date'], $data['batch_id'] ) ) ) ) ); ?>
								</p>
							<?php endif; ?>
							<p><a class="mcc-coa-link" href="<?php echo esc_url( $data['attachment_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View third-party lab results', 'meraki-commerce-core' ); ?></a></p>
						</div>
					</details>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
