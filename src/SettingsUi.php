<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;

use function add_settings_section;

final class SettingsUi
{
    public static function init()
    {
        // Add the setting section
        add_settings_section(
            BridgeInterface::SETTING_SECTION,
            BridgeInterface::SETTING_HEADING_TEXT,
            self::buildSectionHeading(),
            BridgeInterface::TARGET_SECTION
        );

    }

    public static function buildSectionHeading()
    {
        echo '<p>Please use the following fields to confige Xenforo Bridge</p>';
    }

    public static function buildForm()
    {

    }
}
