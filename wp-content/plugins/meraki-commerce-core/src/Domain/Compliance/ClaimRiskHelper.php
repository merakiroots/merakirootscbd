<?php
/**
 * Simple claim-risk helper.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\Compliance;

defined( 'ABSPATH' ) || exit;

/**
 * Flags risky medical-style phrasing.
 */
class ClaimRiskHelper {
	/**
	 * Determine whether a string contains obvious risky claims.
	 *
	 * @param string $content Text to inspect.
	 * @return bool
	 */
	public function has_risky_claims( $content ) {
		$needles = array(
			'cure',
			'treat',
			'prevent disease',
			'heals',
			'prescription replacement',
		);

		$content = strtolower( (string) $content );

		foreach ( $needles as $needle ) {
			if ( false !== strpos( $content, $needle ) ) {
				return true;
			}
		}

		return false;
	}
}
