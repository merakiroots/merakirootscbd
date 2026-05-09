<?php
/**
 * Plugin bootstrapper.
 *
 * @package MerakiCommerceCore
 */

namespace MerakiCommerceCore;

use MerakiCommerceCore\Domain\COA\CoaAdminMetaBox;
use MerakiCommerceCore\Domain\COA\CoaMetaRegistrar;
use MerakiCommerceCore\Domain\COA\CoaMigrationCommand;
use MerakiCommerceCore\Domain\COA\CoaPostType;
use MerakiCommerceCore\Domain\COA\CoaRepository;
use MerakiCommerceCore\Domain\COA\ProductCoaPanel;
use MerakiCommerceCore\Domain\Compliance\ComplianceText;
use MerakiCommerceCore\Domain\Frontend\LabResultsBlock;
use MerakiCommerceCore\Domain\Frontend\LabResultsQuery;
use MerakiCommerceCore\Domain\Frontend\LabResultsShortcode;
use MerakiCommerceCore\Domain\Frontend\ProductCoaPresenter;
use MerakiCommerceCore\Domain\ProductMeta\ProductMetaRegistrar;
use MerakiCommerceCore\Domain\Rest\RestFields;
use MerakiCommerceCore\Support\Assets;
use MerakiCommerceCore\Support\Container;

defined( 'ABSPATH' ) || exit;

/**
 * Wires plugin modules.
 */
final class Bootstrap {
	/**
	 * Shared services.
	 *
	 * @var Container|null
	 */
	private static $container = null;

	/**
	 * Start plugin wiring.
	 *
	 * @return void
	 */
	public static function boot() {
		if ( null !== self::$container ) {
			return;
		}

		self::$container = new Container();
		self::register_services( self::$container );

		add_action( 'init', array( self::$container->get( CoaPostType::class ), 'register' ) );
		add_action( 'init', array( self::$container->get( ProductMetaRegistrar::class ), 'register' ) );
		add_action( 'init', array( self::$container->get( CoaMetaRegistrar::class ), 'register' ) );
		add_action( 'init', array( self::$container->get( LabResultsBlock::class ), 'register' ) );
		add_action( 'init', array( self::$container->get( Assets::class ), 'register_block_assets' ) );
		add_action( 'rest_api_init', array( self::$container->get( RestFields::class ), 'register' ) );
		add_action( 'woocommerce_before_add_to_cart_form', array( self::$container->get( ProductCoaPresenter::class ), 'render_single_product_callout' ) );
		add_shortcode( 'meraki_lab_results', array( self::$container->get( LabResultsShortcode::class ), 'render' ) );

		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( self::$container->get( CoaAdminMetaBox::class ), 'register' ) );
			add_action( 'save_post_mr_coa', array( self::$container->get( CoaAdminMetaBox::class ), 'save' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( self::$container->get( Assets::class ), 'enqueue_admin_assets' ) );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			add_action( 'woocommerce_product_options_general_product_data', array( self::$container->get( ProductCoaPanel::class ), 'render' ) );
			add_action( 'woocommerce_process_product_meta', array( self::$container->get( ProductCoaPanel::class ), 'save' ) );
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			CoaMigrationCommand::register( self::$container->get( CoaRepository::class ) );
		}

		add_action(
			'plugins_loaded',
			static function () {
				load_plugin_textdomain( 'meraki-commerce-core', false, dirname( plugin_basename( MERAKI_COMMERCE_CORE_FILE ) ) . '/languages' );
				update_option( 'meraki_commerce_core_version', MERAKI_COMMERCE_CORE_VERSION );
			}
		);
	}

	/**
	 * Register services with lightweight factories.
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	private static function register_services( Container $container ) {
		$container->set(
			CoaPostType::class,
			static function () {
				return new CoaPostType();
			}
		);
		$container->set(
			ProductMetaRegistrar::class,
			static function () {
				return new ProductMetaRegistrar();
			}
		);
		$container->set(
			CoaMetaRegistrar::class,
			static function () {
				return new CoaMetaRegistrar();
			}
		);
		$container->set(
			ComplianceText::class,
			static function () {
				return new ComplianceText();
			}
		);
		$container->set(
			CoaRepository::class,
			static function () {
				return new CoaRepository();
			}
		);
		$container->set(
			LabResultsQuery::class,
			static function ( Container $c ) {
				return new LabResultsQuery( $c->get( CoaRepository::class ) );
			}
		);
		$container->set(
			LabResultsShortcode::class,
			static function ( Container $c ) {
				return new LabResultsShortcode( $c->get( LabResultsQuery::class ), $c->get( ComplianceText::class ) );
			}
		);
		$container->set(
			LabResultsBlock::class,
			static function ( Container $c ) {
				return new LabResultsBlock( $c->get( LabResultsShortcode::class ) );
			}
		);
		$container->set(
			ProductCoaPresenter::class,
			static function ( Container $c ) {
				return new ProductCoaPresenter( $c->get( CoaRepository::class ), $c->get( ComplianceText::class ) );
			}
		);
		$container->set(
			RestFields::class,
			static function ( Container $c ) {
				return new RestFields( $c->get( CoaRepository::class ) );
			}
		);
		$container->set(
			ProductCoaPanel::class,
			static function ( Container $c ) {
				return new ProductCoaPanel( $c->get( CoaRepository::class ) );
			}
		);
		$container->set(
			CoaAdminMetaBox::class,
			static function () {
				return new CoaAdminMetaBox();
			}
		);
		$container->set(
			Assets::class,
			static function () {
				return new Assets();
			}
		);
	}
}
