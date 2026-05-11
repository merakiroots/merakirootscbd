<?php
/**
 * Plugin Name: My Plugin
 * Description: Local WordPress/WooCommerce plugin.
 * Version: 0.1.0
 * Author: Local Dev
 * Requires at least: 6.5
 * Requires PHP: 8.3
 * Text Domain: my-plugin
 * @package Local\MyPlugin
 * Requires Plugins:  abilities-api
 */

defined('ABSPATH') || exit;

$my_plugin_autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($my_plugin_autoload)) {
    include_once $my_plugin_autoload;
}

add_action(
    'plugins_loaded',
    static function (): void {
        if (class_exists(\Local\MyPlugin\Plugin::class)) {
            \Local\MyPlugin\Plugin::boot();
        }
    }
);


add_action('init', function () {
    if (class_exists(\Local\AbilitiesApi\Plugin::class)) {
        \Local\AbilitiesApi\Plugin::boot();
    }
});
