<?php
/**
 * Theme helper functions.
 *
 * @package MerakiBlockTheme
 * @author Meraki Roots
 * @copyright 2026 Meraki Roots
 * @license GPL-2.0-or-later
 * @link    https://merakirootscbd.com
 */

defined('ABSPATH') || exit;

/**
 * Registers supports and pattern categories.
 *
 * @return void
 */
function meraki_block_theme_setup()
{
    add_theme_support('woocommerce');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('post-thumbnails');
    add_editor_style('assets/css/theme.css');

    register_block_pattern_category(
        'meraki-home',
        array(
            'label' => __('Meraki Home', 'meraki-block-theme'),
        )
    );

    register_nav_menus(
        array(
            'meraki_left_nav' => __('Meraki Left Navigation', 'meraki-block-theme'),
            'meraki_right_nav' => __('Meraki Right Navigation', 'meraki-block-theme'),
            'meraki_mobile_nav' => __('Meraki Mobile Navigation', 'meraki-block-theme'),
            'meraki_footer_nav' => __('Meraki Footer Navigation', 'meraki-block-theme'),
        )
    );
}
add_action('after_setup_theme', 'meraki_block_theme_setup');

/**
 * Registers widget areas used by the first archive pass.
 *
 * @return void
 */
function meraki_block_theme_register_sidebars()
{
    register_sidebar(
        array(
            'name' => __('Shop Sidebar', 'meraki-block-theme'),
            'id' => 'shop-sidebar',
            'description' => __('Optional sidebar used on WooCommerce archive screens.', 'meraki-block-theme'),
            'before_widget' => '<section class="widget meraki-sidebar-widget">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        )
    );
}
add_action('widgets_init', 'meraki_block_theme_register_sidebars');

/**
 * Enqueues frontend assets.
 *
 * @return void
 */
function meraki_block_theme_enqueue_assets()
{
    wp_enqueue_style(
        'meraki-block-theme',
        get_theme_file_uri('/assets/css/theme.css'),
        array(),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'meraki-block-theme',
        get_theme_file_uri('/assets/js/theme.js'),
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'meraki_block_theme_enqueue_assets');

/**
 * Render launch trust cues on cart and checkout.
 *
 * @return void
 */
function meraki_block_theme_render_commerce_assurance()
{
    if (!function_exists('is_cart') || (!is_cart() && !is_checkout())) {
        return;
    }
    ?>
    <section class="meraki-commerce-assurance"
        aria-label="<?php esc_attr_e('Checkout confidence', 'meraki-block-theme'); ?>">
        <div class="meraki-commerce-assurance__item">
            <strong><?php esc_html_e('Free shipping over $100', 'meraki-block-theme'); ?></strong>
            <span><?php esc_html_e('Applied automatically when your order qualifies.', 'meraki-block-theme'); ?></span>
        </div>
        <div class="meraki-commerce-assurance__item">
            <strong><?php esc_html_e('Third-party lab results', 'meraki-block-theme'); ?></strong>
            <span><?php esc_html_e('Current COAs stay linked from product pages and lab results.', 'meraki-block-theme'); ?></span>
        </div>
        <div class="meraki-commerce-assurance__item">
            <strong><?php esc_html_e('Need help?', 'meraki-block-theme'); ?></strong>
            <span><?php esc_html_e('Email info@merakirootscbd.com for product or order support.', 'meraki-block-theme'); ?></span>
        </div>
    </section>
    <?php
}
add_action('woocommerce_before_cart', 'meraki_block_theme_render_commerce_assurance', 5);
add_action('woocommerce_before_checkout_form', 'meraki_block_theme_render_commerce_assurance', 5);
add_action('woocommerce_cart_is_empty', 'meraki_block_theme_render_commerce_assurance', 5);

/**
 * Get gateways that WooCommerce considers available for the current cart/customer.
 *
 * @return array
 */
function meraki_block_theme_get_available_payment_gateways()
{
    if (!function_exists('WC') || !WC() || !method_exists(WC(), 'payment_gateways')) {
        return array();
    }

    $payment_gateways = WC()->payment_gateways();
    if (!$payment_gateways || !method_exists($payment_gateways, 'get_available_payment_gateways')) {
        return array();
    }

    $available_gateways = $payment_gateways->get_available_payment_gateways();

    return is_array($available_gateways) ? $available_gateways : array();
}

/**
 * Determine whether the storefront is visible but checkout payments are not live yet.
 *
 * @return bool
 */
function meraki_block_theme_is_payment_provider_review_mode()
{
    if (!function_exists('WC') || !WC() || !WC()->cart || !WC()->cart->needs_payment()) {
        return false;
    }

    return empty(meraki_block_theme_get_available_payment_gateways());
}

/**
 * Render a clear, public-safe payment-pending notice on cart and checkout.
 *
 * @return void
 */
function meraki_block_theme_render_payment_provider_notice()
{
    if (!function_exists('is_cart') || !function_exists('is_checkout')) {
        return;
    }

    if ((!is_cart() && !is_checkout()) || !meraki_block_theme_is_payment_provider_review_mode()) {
        return;
    }

    if (is_cart() && function_exists('WC') && WC()->cart && WC()->cart->is_empty()) {
        return;
    }
    ?>
    <section class="meraki-payment-review-notice" role="status"
        aria-label="<?php esc_attr_e('Checkout status', 'meraki-block-theme'); ?>">
        <strong><?php esc_html_e('Online checkout is temporarily paused.', 'meraki-block-theme'); ?></strong>
        <span>
            <?php esc_html_e('Payment processing is being connected for merchant review. The storefront is open for browsing, cart review, and provider verification; purchases will open as soon as processing is approved.', 'meraki-block-theme'); ?>
        </span>
    </section>
    <?php
}
add_action('woocommerce_before_cart', 'meraki_block_theme_render_payment_provider_notice', 6);
add_action('woocommerce_before_checkout_form', 'meraki_block_theme_render_payment_provider_notice', 6);

/**
 * Replace WooCommerce's default no-gateway copy with the intentional review-mode message.
 *
 * @param string $message Existing WooCommerce message.
 * @return string
 */
function meraki_block_theme_no_available_payment_methods_message($message)
{
    if (!meraki_block_theme_is_payment_provider_review_mode()) {
        return $message;
    }

    return esc_html__(
        'Online payment processing is being connected for merchant review. Checkout is intentionally paused right now, and purchases will open as soon as a live payment gateway is approved.',
        'meraki-block-theme'
    );
}
add_filter('woocommerce_no_available_payment_methods_message', 'meraki_block_theme_no_available_payment_methods_message');

/**
 * Disable the place-order button while no live gateway exists.
 *
 * @param string $button Existing order button HTML.
 * @return string
 */
function meraki_block_theme_payment_pending_order_button($button)
{
    if (!function_exists('is_checkout') || !is_checkout() || !meraki_block_theme_is_payment_provider_review_mode()) {
        return $button;
    }

    return sprintf(
        '<button type="button" class="button alt meraki-payment-pending__button" disabled aria-disabled="true">%s</button><p class="meraki-payment-pending__note">%s</p>',
        esc_html__('Payment setup pending', 'meraki-block-theme'),
        esc_html__('Checkout cannot submit orders until the approved payment gateway is enabled.', 'meraki-block-theme')
    );
}
add_filter('woocommerce_order_button_html', 'meraki_block_theme_payment_pending_order_button');

/**
 * Default checkout country to the United States for this local storefront.
 *
 * @return string
 */
function meraki_block_theme_default_checkout_country()
{
    return 'US';
}
add_filter('default_checkout_billing_country', 'meraki_block_theme_default_checkout_country');
add_filter('default_checkout_shipping_country', 'meraki_block_theme_default_checkout_country');

/**
 * Render the storefront age gate shell.
 *
 * @return void
 */
function meraki_block_theme_render_age_gate()
{
    if (is_admin() || wp_doing_ajax() || is_feed() || is_preview()) {
        return;
    }
    ?>
    <div class="meraki-age-gate" data-meraki-age-gate hidden>
        <div class="meraki-age-gate__backdrop" aria-hidden="true"></div>
        <section class="meraki-age-gate__dialog" role="dialog" aria-modal="true" aria-labelledby="meraki-age-gate-title"
            aria-describedby="meraki-age-gate-copy">
            <img class="meraki-age-gate__logo" src="<?php echo esc_url(get_theme_file_uri('/assets/images/logo.png')); ?>"
                alt="<?php esc_attr_e('Meraki Roots CBD', 'meraki-block-theme'); ?>">
            <p class="meraki-age-gate__eyebrow"><?php esc_html_e('Age verification', 'meraki-block-theme'); ?></p>
            <h2 id="meraki-age-gate-title"><?php esc_html_e('Are you 21 or older?', 'meraki-block-theme'); ?></h2>
            <p id="meraki-age-gate-copy">
                <?php esc_html_e('Meraki Roots products are intended for adults. Please confirm your age before entering the store.', 'meraki-block-theme'); ?>
            </p>
            <div class="meraki-age-gate__actions">
                <button type="button" class="meraki-age-gate__button meraki-age-gate__button--primary"
                    data-meraki-age-accept><?php esc_html_e('Yes, enter site', 'meraki-block-theme'); ?></button>
                <a class="meraki-age-gate__button meraki-age-gate__button--secondary" href="https://www.google.com/"
                    rel="nofollow noopener"><?php esc_html_e('No, leave site', 'meraki-block-theme'); ?></a>
            </div>
            <p class="meraki-age-gate__note">
                <?php esc_html_e('By entering, you agree that you meet the legal age requirement for your location.', 'meraki-block-theme'); ?>
            </p>
        </section>
    </div>
    <?php
}
add_action('wp_footer', 'meraki_block_theme_render_age_gate', 5);