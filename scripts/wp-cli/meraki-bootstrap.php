<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "Run with: wp eval-file scripts/wp-cli/meraki-bootstrap.php\n");
    exit(1);
}

function mr_bootstrap_page(string $title, string $slug, string $content = '', string $template = ''): int
{
    $page = get_page_by_path($slug, OBJECT, 'page');
    if ($page) {
        $page_id = (int) $page->ID;
        wp_update_post([
            'ID' => $page_id,
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
        ]);
    } else {
        $page_id = wp_insert_post([
            'post_type' => 'page',
            'post_title' => $title,
            'post_name' => $slug,
            'post_content' => $content,
            'post_status' => 'publish',
        ], true);
        if (is_wp_error($page_id)) {
            WP_CLI::warning('Could not create page ' . $title . ': ' . $page_id->get_error_message());
            return 0;
        }
    }

    if ($template) {
        update_post_meta($page_id, '_wp_page_template', $template);
    }

    return (int) $page_id;
}

function mr_bootstrap_term(string $taxonomy, string $name, string $slug, string $description = '', int $parent = 0): int
{
    $existing = get_term_by('slug', $slug, $taxonomy);
    if ($existing && !is_wp_error($existing)) {
        wp_update_term((int) $existing->term_id, $taxonomy, [
            'description' => $description,
            'parent' => $parent,
        ]);
        return (int) $existing->term_id;
    }

    $result = wp_insert_term($name, $taxonomy, [
        'slug' => $slug,
        'description' => $description,
        'parent' => $parent,
    ]);

    if (is_wp_error($result)) {
        WP_CLI::warning('Could not create term ' . $name . ': ' . $result->get_error_message());
        return 0;
    }

    return (int) $result['term_id'];
}

function mr_bootstrap_menu(string $name): int
{
    $menu = wp_get_nav_menu_object($name);
    if ($menu) {
        return (int) $menu->term_id;
    }
    $menu_id = wp_create_nav_menu($name);
    return is_wp_error($menu_id) ? 0 : (int) $menu_id;
}

function mr_bootstrap_menu_has_item(int $menu_id, string $title, string $url): bool
{
    $items = wp_get_nav_menu_items($menu_id) ?: [];
    foreach ($items as $item) {
        if ($item->title === $title || rtrim($item->url, '/') === rtrim($url, '/')) {
            return true;
        }
    }
    return false;
}

function mr_bootstrap_add_url_item(int $menu_id, string $title, string $url, int $parent = 0): int
{
    if (!$menu_id || mr_bootstrap_menu_has_item($menu_id, $title, $url)) {
        return 0;
    }
    $item_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title' => $title,
        'menu-item-url' => $url,
        'menu-item-status' => 'publish',
        'menu-item-parent-id' => $parent,
    ]);
    return is_wp_error($item_id) ? 0 : (int) $item_id;
}

$home = mr_bootstrap_page('Home', 'home');
$shop = mr_bootstrap_page('Shop', 'shop');
$lab = mr_bootstrap_page('Lab Results', 'lab-results', '', 'page-lab-results');
$learn = mr_bootstrap_page('Learn', 'learn');
$about = mr_bootstrap_page('About', 'about', '<p>Meraki Roots is a premium CBD brand rooted in transparency, creativity, and care. Add your full founder story here before launch.</p>');
$contact = mr_bootstrap_page('Contact', 'contact', '<p>Email <a href="mailto:info@merakirootscbd.com">info@merakirootscbd.com</a> for order questions, product support, wholesale inquiries, or general Meraki Roots support.</p>');
$partner = mr_bootstrap_page('Partner', 'partner', '<p>Meraki Roots works with retailers and wellness partners that value transparent product information, third-party lab documentation, and a clean customer experience.</p><p>Email <a href="mailto:info@merakirootscbd.com">info@merakirootscbd.com</a> with your business name, location, and the products you are interested in carrying.</p>');
$faq = mr_bootstrap_page('FAQs', 'faqs', '<h2>Shipping</h2><p>Free shipping is available on orders over $100.</p><h2>Lab Results</h2><p>Product-level lab results are available on product pages and on the Lab Results page.</p>');
$privacy = mr_bootstrap_page('Privacy Policy', 'privacy-policy', '<p>Meraki Roots uses customer information to process orders, provide support, communicate about purchases, and improve the storefront experience. For privacy questions, email <a href="mailto:info@merakirootscbd.com">info@merakirootscbd.com</a>.</p>');
$refund = mr_bootstrap_page('Refund Policy', 'refund-policy', '<p>If an order arrives damaged, incorrect, or incomplete, email <a href="mailto:info@merakirootscbd.com">info@merakirootscbd.com</a> with the order number and photos when applicable. Refund and replacement requests are reviewed case by case.</p>');
$shipping = mr_bootstrap_page('Shipping Policy', 'shipping-policy', '<p>Orders ship to eligible U.S. destinations where products can be sold and delivered. Free shipping is available on orders over $100. Tracking details are sent when an order is fulfilled.</p>');
$terms = mr_bootstrap_page('Terms of Service', 'terms-of-service', '<p>By using this site or placing an order, customers agree to provide accurate order information, comply with applicable laws, and use Meraki Roots products only as directed. Products are intended for adults.</p>');
$cart = mr_bootstrap_page('Cart', 'cart', '[woocommerce_cart]');
$checkout = mr_bootstrap_page('Checkout', 'checkout', '[woocommerce_checkout]');
$account = mr_bootstrap_page('My Account', 'my-account', '[woocommerce_my_account]');

if ($home) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $home);
}
if ($privacy) {
    update_option('wp_page_for_privacy_policy', $privacy);
}
if (class_exists('WooCommerce')) {
    update_option('woocommerce_shop_page_id', $shop);
    update_option('woocommerce_cart_page_id', $cart);
    update_option('woocommerce_checkout_page_id', $checkout);
    update_option('woocommerce_myaccount_page_id', $account);
}

update_option('permalink_structure', '/%postname%/');

if (taxonomy_exists('product_cat')) {
    $tinctures = mr_bootstrap_term('product_cat', 'Tinctures', 'tinctures', 'Simple CBD tinctures in multiple strengths, made with CBD isolate and fractionated coconut oil.');
    $capsules = mr_bootstrap_term('product_cat', 'Capsules', 'capsules', 'Capsule-based CBD formats for simple, measured serving routines.');
    $vapes = mr_bootstrap_term('product_cat', 'Vape Cartridges', 'vape-cartridges', 'Terpene-forward CBD vape cartridges intended for adult customers using compatible vaporizer hardware.');
    $terps = mr_bootstrap_term('product_cat', 'Terpsolate Diamonds', 'terpsolate-diamonds', 'Concentrate-style CBD isolate products with terpene profiles inspired by familiar strain aromas.');
    $topicals = mr_bootstrap_term('product_cat', 'Topicals', 'topicals', 'CBD body lotions made for external-use skin-care and body-care routines.');
    $body_lotion = mr_bootstrap_term('product_cat', 'Body Lotion', 'body-lotion', 'CBD body lotions for skin-care and body-care routines.', $topicals);
    $merch = mr_bootstrap_term('product_cat', 'Merch', 'merch', 'Meraki Roots apparel and non-CBD merchandise.');
}

$left = mr_bootstrap_menu('Meraki Left Navigation');
$right = mr_bootstrap_menu('Meraki Right Navigation');
$mobile = mr_bootstrap_menu('Meraki Mobile Navigation');
$footer = mr_bootstrap_menu('Meraki Footer Navigation');

mr_bootstrap_add_url_item($left, 'Shop', home_url('/shop/'));
mr_bootstrap_add_url_item($left, 'Learn', home_url('/learn/'));
mr_bootstrap_add_url_item($left, 'About', home_url('/about/'));

mr_bootstrap_add_url_item($right, 'Lab Results', home_url('/lab-results/'));
mr_bootstrap_add_url_item($right, 'Partner', home_url('/partner/'));
mr_bootstrap_add_url_item($right, 'Contact', home_url('/contact/'));

$shop_parent = mr_bootstrap_add_url_item($mobile, 'Shop', home_url('/shop/'));
mr_bootstrap_add_url_item($mobile, 'Tinctures', home_url('/product-category/tinctures/'), $shop_parent);
mr_bootstrap_add_url_item($mobile, 'Capsules', home_url('/product-category/capsules/'), $shop_parent);
mr_bootstrap_add_url_item($mobile, 'Vape Cartridges', home_url('/product-category/vape-cartridges/'), $shop_parent);
mr_bootstrap_add_url_item($mobile, 'Terpsolate Diamonds', home_url('/product-category/terpsolate-diamonds/'), $shop_parent);
mr_bootstrap_add_url_item($mobile, 'Topicals', home_url('/product-category/topicals/'), $shop_parent);
mr_bootstrap_add_url_item($mobile, 'Lab Results', home_url('/lab-results/'));
mr_bootstrap_add_url_item($mobile, 'Learn', home_url('/learn/'));
mr_bootstrap_add_url_item($mobile, 'Partner', home_url('/partner/'));
mr_bootstrap_add_url_item($mobile, 'Contact', home_url('/contact/'));
mr_bootstrap_add_url_item($mobile, 'My Account', home_url('/my-account/'));
mr_bootstrap_add_url_item($mobile, 'Cart', home_url('/cart/'));

foreach ([
    'Shop' => '/shop/',
    'Lab Results' => '/lab-results/',
    'FAQs' => '/faqs/',
    'Contact' => '/contact/',
    'Partner' => '/partner/',
    'Privacy Policy' => '/privacy-policy/',
    'Refund Policy' => '/refund-policy/',
    'Shipping Policy' => '/shipping-policy/',
    'Terms of Service' => '/terms-of-service/',
] as $title => $path) {
    mr_bootstrap_add_url_item($footer, $title, home_url($path));
}

$locations = get_theme_mod('nav_menu_locations', []);
$locations['meraki_left_nav'] = $left;
$locations['meraki_right_nav'] = $right;
$locations['meraki_mobile_nav'] = $mobile;
$locations['meraki_footer_nav'] = $footer;
set_theme_mod('nav_menu_locations', $locations);

flush_rewrite_rules();
WP_CLI::success('Meraki Roots baseline pages, menus, categories, and core options are ready.');
