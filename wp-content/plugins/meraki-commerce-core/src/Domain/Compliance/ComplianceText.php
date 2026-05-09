<?php
/**
 * Shared compliance strings.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\Compliance;

defined( 'ABSPATH' ) || exit;

/**
 * Holds standard compliance-facing copy.
 */
class ComplianceText {
	/**
	 * FDA disclaimer used across theme and plugin surfaces.
	 *
	 * @return string
	 */
	public function get_fda_disclaimer() {
		return __( 'These products have not been evaluated by the Food & Drug Administration and are not intended to diagnose, treat, cure, or prevent any disease.', 'meraki-commerce-core' );
	}
}
