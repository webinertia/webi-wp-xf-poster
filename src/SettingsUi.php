<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;
use WebiXfBridge\Settings;

use function add_settings_field;
use function add_settings_section;
use function checked;
use function get_option;
use function register_setting;

final class SettingsUi
{
    private const HEADING_TEXT = '<p>Please use the following fields to confige Xenforo Bridge</p>';
    public static function init()
    {
        require_once BridgeInterface::WP_ADMIN_PATH . 'plugin.php';
        require_once BridgeInterface::WP_ADMIN_PATH . 'template.php';
        // Add the setting section
        add_settings_section(
            BridgeInterface::SETTING_SECTION,
            BridgeInterface::SETTING_HEADING_TEXT,
            self::buildSectionHeading(...),
            BridgeInterface::TARGET_SECTION
        );
        // add the enable checkbox
        add_settings_field(
            Settings::enableBridgeSetting->value,
            Settings::enableBridgeSettingsText->value,
            self::enableBridgeField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        add_settings_field(
            Settings::postExcerptSetting->value,
            Settings::postExcerptSettingText->value,
            self::postExcerptField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        add_settings_field(
            Settings::apiUrlSetting->value,
            Settings::apiUrlSettingText->value,
            self::apiUrlField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        add_settings_field(
            Settings::apiKeySetting->value,
            Settings::apiKeySettingText->value,
            self::apiKeyField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        add_settings_field(
            Settings::targetTagsSetting->value,
            Settings::targetTagsSettingText->value,
            self::allowedTagsField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        add_settings_field(
            Settings::nodeIdSetting->value,
            Settings::nodeIdSettingText->value,
            self::nodeIdField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        add_settings_field(
            Settings::xfUserIdSetting->value,
            Settings::xfUserIdSettingText->value,
            self::xfUserIdField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        register_setting(BridgeInterface::TARGET_SECTION, Settings::enableBridgeSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::postExcerptSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::apiUrlSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::apiKeySetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::targetTagsSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::nodeIdSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::xfUserIdSetting->value);
    }

    public static function buildSectionHeading()
    {
        echo self::HEADING_TEXT;
    }

    public static function enableBridgeField()
    {
        echo '<input name="'. Settings::enableBridgeSetting->value
        . '" id="'. Settings::enableBridgeSetting->value
        . '" type="checkbox" value="1" class="code" '
        . checked(1, get_option(Settings::enableBridgeSetting->value), false)
        . ' /> '
        . Settings::enableBridgeSettingsText->value;
    }

    public static function postExcerptField()
    {
        echo '<input name="'. Settings::postExcerptSetting->value
        . '" id="'. Settings::postExcerptSetting->value
        . '" type="checkbox" value="1" class="code" '
        . checked(1, get_option(Settings::postExcerptSetting->value), false)
        . ' /> '
        . Settings::postExcerptSettingText->value;
    }

    public static function apiUrlField()
    {
        echo '<input name="' . Settings::apiUrlSetting->value
        . '" id="' . Settings::apiUrlSetting->value
        . '" value="' . get_option(Settings::apiUrlSetting->value) . '"'
        . '" type="text" minlength="5" maxlength="253" size="50" />';
    }

    public static function apiKeyField()
    {
        echo '<input name="' . Settings::apiKeySetting->value
        . '" id="' . Settings::apiKeySetting->value
        . '" value="' . get_option(Settings::apiKeySetting->value) . '"'
        . '" type="text" minlength="5" maxlength="253" size="50" />';
    }

    public static function allowedTagsField()
    {
        $savedTags = get_option(Settings::targetTagsSetting->value);
        if (! $savedTags) {
            $savedTags = implode(',', self::$allowedTags);
        }
        echo '<input name="' . Settings::targetTagsSetting->value
        . '" id="' . Settings::targetTagsSetting->value
        . '" value="' . $savedTags . '"'
        . '" type="text" minlength="5" maxlength="253" size="50" />';
    }

    public static function nodeIdField()
    {
        echo '<input name="' . Settings::nodeIdSetting->value
        . '" id="' . Settings::nodeIdSetting->value
        . '" value="' . get_option(Settings::nodeIdSetting->value) . '"'
        . '" type="text" minlength="1" maxlength="10" size="10" />';
    }

    public static function xfUserIdField()
    {
        echo '<input name="' . Settings::xfUserIdSetting->value
        . '" id="' . Settings::xfUserIdSetting->value
        . '" value="' . get_option(Settings::xfUserIdSetting->value) . '"'
        . '" type="text" minlength="1" maxlength="10" size="10" />';
    }
}
