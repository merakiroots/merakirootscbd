<?php
/**
 * Single product content template.
 *
 * @package MerakiBlockTheme
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$product_id   = $product ? $product->get_id() : get_the_ID();
$product_form = (string) get_post_meta( $product_id, '_mr_product_form', true );
?>
<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'meraki-single-product', $product ); ?>>
	<?php do_action( 'woocommerce_before_single_product' ); ?>

	<div class="meraki-single-product__grid">
		<div class="meraki-single-product__gallery">
			<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
		</div>

		<div class="meraki-single-product__summary">
			<?php if ( $product_form ) : ?>
				<div class="meraki-product-kicker">
					<?php echo esc_html( $product_form ); ?>
				</div>
			<?php endif; ?>

			<h1 class="product_title entry-title"><?php the_title(); ?></h1>
			<div class="meraki-single-product__price"><?php woocommerce_template_single_price(); ?></div>

			<?php if ( $product && $product->get_short_description() ) : ?>
				<div class="meraki-single-product__overview"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
			<?php endif; ?>

			<div class="meraki-single-product__cart">
				<?php woocommerce_template_single_add_to_cart(); ?>
			</div>
		</div>
	</div>

	<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
</article>
