<?php
/**
 * Main plugin bootstrap class.
 *
 * @package Local\MyPlugin
 * @category Plugin
 * @author Your Name <you@example.com>
 * @license GPL-2.0-or-later
 * @link https://example.com
 * @since 1.0.0
 * @php 8.0
 */

namespace Local\MyPlugin;

defined('ABSPATH') || exit;

/**
 * Main plugin class.
 */
final class Plugin
{

    /**
     * Boot the plugin.
     */
    public static function boot(): void
    {
        add_action('init', [self::class, 'init']);
    }

    /**
     * Initialize plugin behavior.
     */
    public static function init(): void
    {
        error_log('Local MyPlugin initialized.');
    }
}
