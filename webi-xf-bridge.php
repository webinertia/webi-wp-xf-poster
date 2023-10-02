<?php
/*
 * Plugin Name:       Webinertia Xenforo Bridge
 * Description: Bridges Wordpress with a configured XenForo forum. This bridge is for sending WP articles to Xenforo via API.
 * Version:           0.0.1
 * Requires at least: 6.3.1
 * Requires PHP:      8.2.0
 * Author:            Webinertia
 * Author URI:        https://github.com/orgs/webinertia/discussions
 * License:           BSD-3-Clause
 */
declare(strict_types=1);

use WebiXfBridge\Bridge;
use WebiXfBridge\Settings;
use WebiXfBridge\SettingsUi;

require_once plugin_dir_path(__FILE__) . 'lib/autoload.php';
$bridge = new Bridge();
$enableBridge = get_option(Settings::enableBridgeSetting->value);
if ((bool) $enableBridge) {
    add_action(
        'transition_post_status',
        function ($old, $new, $post) use ($bridge) {
            $bridge->handleTransition($old, $new, $post);
        },
        10,
        3
    );
}
// Admin Settings "write" section
add_action(
    'admin_init',
    SettingsUi::init(...)
);
