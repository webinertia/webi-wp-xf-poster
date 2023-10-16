<?php
/*
 * Plugin Name:       Webinertia Xenforo Bridge
 * Description:       Bridges Wordpress with a configured XenForo forum. This bridge is for sending WP articles to Xenforo via API.
 * Version:           1.0.1
 * Requires at least: 6.3.1
 * Requires PHP:      8.2.0
 * Author:            Webinertia
 * Author URI:        https://github.com/orgs/webinertia/discussions
 * License:           BSD-3-Clause
 */
declare(strict_types=1);

use WebiXfBridge\BridgePost;
use WebiXfBridge\Settings;
use WebiXfBridge\SettingsUi;

require_once plugin_dir_path(__FILE__) . 'lib/autoload.php';

$enableBridge = get_option(Settings::enableBridgeSetting->value);

if ($enableBridge) {
    $bridge = new BridgePost(); // currently this is the only type (post type) that we support
}

// Admin Settings "write" section, we have to have this or we can not enable the bridge ;)
add_action(
    'admin_init',
    SettingsUi::init(...)
);
