<?php
/**
 * COA normalization helpers.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Domain\COA;

defined( 'ABSPATH' ) || exit;

/**
 * Sanitizes and normalizes COA inputs.
 */
class CoaNormalizer {
	/**
	 * Normalize a date string to Y-m-d if possible.
	 *
	 * @param string $value Raw date input.
	 * @return string
	 */
	public static function normalize_date( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		$timestamp = strtotime( $value );

		if ( false === $timestamp ) {
			return sanitize_text_field( $value );
		}

		return gmdate( 'Y-m-d', $timestamp );
	}

	/**
	 * Normalize a status slug.
	 *
	 * @param string $value Raw status.
	 * @return string
	 */
	public static function normalize_status( $value ) {
		$allowed = array( 'current', 'archived', 'superseded' );
		$value   = sanitize_key( $value );

		return in_array( $value, $allowed, true ) ? $value : 'current';
	}

	/**
	 * Normalize a comma-separated product list.
	 *
	 * @param mixed $value Raw IDs.
	 * @return array<int>
	 */
	public static function normalize_product_ids( $value ) {
		if ( is_array( $value ) ) {
			$ids = $value;
		} else {
			$ids = preg_split( '/\s*,\s*/', (string) $value );
		}

		$ids = array_filter( array_map( 'absint', $ids ) );
		return array_values( array_unique( $ids ) );
	}

	/**
	 * Normalize a URL-like value.
	 *
	 * @param string $value Raw URL.
	 * @return string
	 */
	public static function normalize_url( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		return esc_url_raw( $value );
	}
}
