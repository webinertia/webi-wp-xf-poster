<?php

declare(strict_types=1);

namespace WebiXfBridge;

interface BridgeInterface
{
    // root setting namespace to avoid column collision
    public const PLUGIN_NAMESPACE = 'webi_xf_bridge_';
    // Http messaging related const
    public const POST_HEADER_TYPE     = 'application/x-www-form-urlencoded';
    public const DELETE_HEADER_TYPE   = self::POST_HEADER_TYPE;
    public const HTTP_VERSION         = '1.1';
    public const HTTP_DELETE_METHOD   = 'DELETE';
    public const HTTP_GET_METHOD      = 'GET';
    public const HTTP_POST_METHOD     = 'POST';
    // todo: search this and remove if not used
    public const ALLOWED_HTTP_METHODS = [
        self::HTTP_GET_METHOD,
        self::HTTP_POST_METHOD,
        self::HTTP_DELETE_METHOD,
    ];
    // post meta data
    public const SAVE_POST_META   = self::PLUGIN_NAMESPACE . 'save_meta_action';
    public const POST_ID_COLUMN   = self::PLUGIN_NAMESPACE . 'post_id';
    public const THREAD_ID_COLUMN = self::PLUGIN_NAMESPACE . 'thread_id';
    // paths? Maybe....
    public const WP_ADMIN_PATH    = __DIR__ . '/../../../../wp-admin/includes/';
    public const WP_INCLUDES_PATH = __DIR__ . '/../../../../wp-includes/';
    // Admin Settings UI const
    public const SETTING_HEADING_TEXT = 'Xenforo Bridge Settings';
    public const SETTING_SECTION      = 'webi_xf_bridge_section';
    public const TARGET_SECTION       = 'writing';
}
