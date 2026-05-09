<?php
/**
 * Title: Home Hero
 * Slug: meraki-block-theme/home-hero
 * Categories: meraki-home
 * Inserter: no
 *
 * @package MerakiBlockTheme
 */

$hero = esc_url( get_theme_file_uri( '/assets/images/category-tinctures.jpg' ) );
?>
<!-- wp:cover {"url":"<?php echo $hero; ?>","dimRatio":45,"overlayColor":"ink","minHeight":640,"className":"meraki-home-hero","layout":{"type":"constrained"}} -->
<div class="wp-block-cover meraki-home-hero" style="min-height:640px"><span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-45 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="<?php echo $hero; ?>" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"56px","bottom":"56px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:56px;padding-bottom:56px"><!-- wp:paragraph {"textColor":"canvas","fontSize":"sm"} -->
<p class="has-canvas-color has-text-color has-sm-font-size">Meraki Roots CBD</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"textColor":"canvas","fontSize":"hero"} -->
<h1 class="wp-block-heading has-canvas-color has-text-color has-hero-font-size">CBD made clear from product to lab result.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"canvas","fontSize":"md"} -->
<p class="has-canvas-color has-text-color has-md-font-size">Shop tinctures, capsules, topicals, vapes, and terpsolate diamonds with third-party COAs linked at the product level.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"canvas","textColor":"ink"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-ink-color has-canvas-background-color has-text-color has-background wp-element-button" href="/shop/">Shop Now</a></div>
<!-- /wp:button -->

<!-- wp:button {"textColor":"canvas","className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-canvas-color has-text-color wp-element-button" href="/lab-results/">View Lab Results</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->
