<?php
/**
 * Dynamic block registration for lab results.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the block wrapper.
 */
class LabResultsBlock {
	/**
	 * Shortcode renderer dependency.
	 *
	 * @var LabResultsShortcode
	 */
	private $shortcode;

	/**
	 * Constructor.
	 *
	 * @param LabResultsShortcode $shortcode Shortcode renderer.
	 */
	public function __construct( LabResultsShortcode $shortcode ) {
		$this->shortcode = $shortcode;
	}

	/**
	 * Register the block from metadata.
	 *
	 * @return void
	 */
	public function register() {
		$block_dir = MERAKI_COMMERCE_CORE_DIR . 'assets/blocks/lab-results';

		if ( ! file_exists( $block_dir . '/block.json' ) ) {
			return;
		}

		register_block_type(
			$block_dir,
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
	}

	/**
	 * Render callback.
	 *
	 * @param array<string,mixed> $attributes Block attributes.
	 * @return string
	 */
	public function render( $attributes ) {
		return $this->shortcode->render( $attributes );
	}
}
