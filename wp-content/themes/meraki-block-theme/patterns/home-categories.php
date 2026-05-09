<?php
/**
 * Title: Home Categories
 * Slug: meraki-block-theme/home-categories
 * Categories: meraki-home
 * Inserter: no
 *
 * @package MerakiBlockTheme
 */

$cards = array(
	array(
		'title' => 'Tinctures',
		'url'   => '/product-category/tinctures/',
		'image' => get_theme_file_uri( '/assets/images/category-tinctures.jpg' ),
	),
	array(
		'title' => 'Capsules',
		'url'   => '/product-category/capsules/',
		'image' => get_theme_file_uri( '/assets/images/category-capsules.jpg' ),
	),
	array(
		'title' => 'Vape Cartridges',
		'url'   => '/product-category/vape-cartridges/',
		'image' => get_theme_file_uri( '/assets/images/category-vapes.jpg' ),
	),
	array(
		'title' => 'Terpsolate Diamonds',
		'url'   => '/product-category/terpsolate-diamonds/',
		'image' => get_theme_file_uri( '/assets/images/category-terpsolate.jpg' ),
	),
	array(
		'title' => 'Topicals',
		'url'   => '/product-category/topicals/',
		'image' => get_theme_file_uri( '/assets/images/category-topicals.jpg' ),
	),
);
?>
<!-- wp:group {"tagName":"section","className":"meraki-category-mosaic","layout":{"type":"constrained"}} -->
<section class="wp-block-group meraki-category-mosaic"><!-- wp:columns {"align":"full","className":"meraki-category-mosaic__grid"} -->
<div class="wp-block-columns alignfull meraki-category-mosaic__grid">
<?php foreach ( $cards as $card ) : ?>
	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:cover {"url":"<?php echo esc_url( $card['image'] ); ?>","dimRatio":0,"minHeight":280,"isDark":false,"className":"meraki-category-card","layout":{"type":"constrained"}} -->
	<div class="wp-block-cover is-light meraki-category-card" style="min-height:280px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( $card['image'] ); ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"className":"meraki-category-card__content","layout":{"type":"constrained"}} -->
	<div class="wp-block-group meraki-category-card__content"><!-- wp:heading {"level":3,"textColor":"canvas","fontSize":"lg"} -->
	<h3 class="wp-block-heading has-canvas-color has-text-color has-lg-font-size"><?php echo esc_html( $card['title'] ); ?></h3>
	<!-- /wp:heading -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"canvas","textColor":"ink"} -->
	<div class="wp-block-button"><a class="wp-block-button__link has-ink-color has-canvas-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( $card['url'] ); ?>">Shop Now</a></div>
	<!-- /wp:button --></div>
	<!-- /wp:buttons --></div>
	<!-- /wp:group --></div></div>
	<!-- /wp:cover --></div>
	<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
