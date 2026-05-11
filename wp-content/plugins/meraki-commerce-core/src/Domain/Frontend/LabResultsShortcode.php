<?php
/**
 * Shortcode renderer for lab results.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\Frontend;

use MerakiCommerceCore\Domain\Compliance\ComplianceText;

defined( 'ABSPATH' ) || exit;

/**
 * Renders grouped lab results.
 */
class LabResultsShortcode {
	/**
	 * Query layer.
	 *
	 * @var LabResultsQuery
	 */
	private $query;

	/**
	 * Compliance strings.
	 *
	 * @var ComplianceText
	 */
	private $compliance_text;

	/**
	 * Constructor.
	 *
	 * @param LabResultsQuery $query Query instance.
	 * @param ComplianceText  $compliance_text Compliance helper.
	 */
	public function __construct( LabResultsQuery $query, ComplianceText $compliance_text ) {
		$this->query           = $query;
		$this->compliance_text = $compliance_text;
	}

	/**
	 * Render the lab results listing.
	 *
	 * @param array<string,mixed> $atts Shortcode attributes.
	 * @return string
	 */
	public function render( $atts = array() ) {
		$atts    = shortcode_atts(
			array(
				'status' => 'current',
			),
			$atts,
			'meraki_lab_results'
		);
		$results = $this->query->get_grouped_results( $atts );

		ob_start();
		?>
		<div class="meraki-lab-results">
			<?php if ( empty( $results ) ) : ?>
				<p><?php esc_html_e( 'No lab results are available yet.', 'meraki-commerce-core' ); ?></p>
			<?php else : ?>
				<?php foreach ( $results as $group_label => $items ) : ?>
					<details class="meraki-lab-results__group">
						<summary class="meraki-lab-results__summary"><?php echo esc_html( $group_label ); ?></summary>
						<ul class="meraki-lab-results__list">
							<?php foreach ( $items as $item ) : ?>
								<li class="meraki-lab-results__item">
									<?php
									$title = isset( $item['product_title'] ) && $item['product_title'] ? $item['product_title'] : $item['title'];
									?>
									<a href="<?php echo esc_url( $item['attachment_url'] ); ?>" target="_blank" rel="noopener noreferrer">
										<?php echo esc_html( $title ); ?><span class="screen-reader-text"><?php esc_html_e( ' View PDF', 'meraki-commerce-core' ); ?></span>
									</a>
									<span class="meraki-lab-results__item-meta">
										<?php echo esc_html( trim( implode( ' | ', array_filter( array( $item['lab_name'], $item['test_date'] ) ) ) ) ); ?>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</details>
				<?php endforeach; ?>
			<?php endif; ?>
			<p class="meraki-lab-results__disclaimer"><?php echo esc_html( $this->compliance_text->get_fda_disclaimer() ); ?></p>
		</div>
		<?php
		return trim( (string) ob_get_clean() );
	}
}
