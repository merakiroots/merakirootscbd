<?php
/**
 * Minimal service container.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Small factory-backed container.
 */
class Container {
	/**
	 * Factories keyed by class name.
	 *
	 * @var array<string,callable>
	 */
	private $factories = array();

	/**
	 * Materialized instances keyed by class name.
	 *
	 * @var array<string,object>
	 */
	private $instances = array();

	/**
	 * Store a factory callback.
	 *
	 * @param string   $key     Service key.
	 * @param callable $factory Factory callback.
	 * @return void
	 */
	public function set( $key, callable $factory ) {
		$this->factories[ $key ] = $factory;
	}

	/**
	 * Resolve a service.
	 *
	 * @param string $key Service key.
	 * @return object
	 * @throws \RuntimeException When the service is not registered.
	 */
	public function get( $key ) {
		if ( isset( $this->instances[ $key ] ) ) {
			return $this->instances[ $key ];
		}

		if ( ! isset( $this->factories[ $key ] ) ) {
			throw new \RuntimeException(
				sprintf(
					'Service "%s" is not registered.',
					esc_html( $key )
				)
			);
		}

		$this->instances[ $key ] = call_user_func( $this->factories[ $key ], $this );
		return $this->instances[ $key ];
	}
}
