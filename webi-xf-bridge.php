<?php

declare(strict_types=1);

use WebiXfBridge\Bridge;

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
require_once plugin_dir_path(__FILE__) . 'lib/autoload.php';
$bridge = new Bridge();
add_action(
    'on_all_status_transitions',
    function($oldStatus, $newStatus, $post) use($bridge) {
        $bridge->actionHandler($oldStatus, $newStatus, $post);
    },
    10,
    3
);