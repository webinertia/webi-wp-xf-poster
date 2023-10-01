<?php

declare(strict_types=1);

namespace WebiXfBridge;

interface BridgeInterface
{
    public const WP_ADMIN_PATH        = __DIR__ . '/../../../../wp-admin/includes/';
    public const WP_INCLUDES_PATH     = __DIR__ . '/../../../../wp-includes/';
    public const PLUGIN_NAMESPACE     = 'webi_xf_bridge_';
    public const SETTING_HEADING_TEXT = 'Xenforo Bridge Settings';
    public const SETTING_SECTION      = 'webi_xf_bridge_section';
    public const TARGET_SECTION       = 'writing';
    public const XF_DOMAIN_FIELD      = 'webi_xf_bridge';
}
