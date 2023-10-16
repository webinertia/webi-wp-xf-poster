<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;
use WebiXfBridge\Formatter;
use WebiXfBridge\Settings;

use function add_action;
use function add_settings_field;
use function add_settings_section;
use function checked;
use function esc_attr;
use function esc_html__;
use function get_option;
use function get_post_meta;
use function register_setting;
use function wp_nonce_field;

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
            Settings::deleteXfThreadSetting->value,
            Settings::deleteXfThreadSettingText->value,
            self::deleteXfThreadField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        add_settings_field(
            Settings::useFeaturedImageSetting->value,
            Settings::useFeaturedImageSettingText->value,
            self::useFeaturedImageField(...),
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
            Settings::excerptWordCountSetting->value,
            Settings::excerptWordCountSettingText->value,
            self::excerptWordCountField(...),
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
        add_settings_field(
            Settings::xfPostImageWidthSetting->value,
            Settings::xfPostImageWidthSettingText->value,
            self::xfImageWidthField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        add_settings_field(
            Settings::xfPostImageHeightSetting->value,
            Settings::xfPostImageHeightSettingText->value,
            self::xfImageHeightField(...),
            BridgeInterface::TARGET_SECTION,
            BridgeInterface::SETTING_SECTION
        );
        // register settings
        register_setting(BridgeInterface::TARGET_SECTION, Settings::enableBridgeSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::postExcerptSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::deleteXfThreadSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::useFeaturedImageSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::excerptWordCountSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::apiUrlSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::apiKeySetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::targetTagsSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::nodeIdSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::xfUserIdSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::xfPostImageHeightSetting->value);
        register_setting(BridgeInterface::TARGET_SECTION, Settings::xfPostImageWidthSetting->value);
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

    public static function deleteXfThreadField()
    {
        echo '<input name="'. Settings::deleteXfThreadSetting->value
        . '" id="'. Settings::deleteXfThreadSetting->value
        . '" type="checkbox" value="1" class="code" '
        . checked(1, get_option(Settings::deleteXfThreadSetting->value), false)
        . ' /> '
        . Settings::deleteXfThreadSettingText->value;
    }

    public static function useFeaturedImageField()
    {
        echo '<input name="'. Settings::useFeaturedImageSetting->value
        . '" id="'. Settings::useFeaturedImageSetting->value
        . '" type="checkbox" value="1" class="code" '
        . checked(1, get_option(Settings::useFeaturedImageSetting->value), false)
        . ' /> '
        . Settings::useFeaturedImageSettingText->value;
    }

    public static function excerptWordCountField()
    {
        echo '<input name="' . Settings::excerptWordCountSetting->value
        . '" id="' . Settings::excerptWordCountSetting->value
        . '" value="' . get_option(Settings::excerptWordCountSetting->value) . '"'
        . '" type="text" minlength="1" maxlength="10" size="10" />';
    }

    public static function apiUrlField()
    {
        echo '<input name="' . Settings::apiUrlSetting->value
        . '" id="' . Settings::apiUrlSetting->value
        . '" value="' . get_option(Settings::apiUrlSetting->value) . '"'
        . '" type="text" minlength="5" maxlength="261" size="50" />';
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
            $savedTags = implode(',', Formatter::$targetTags);
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

    public static function xfImageHeightField()
    {
        echo '<input name="' . Settings::xfPostImageHeightSetting->value
        . '" id="' . Settings::xfPostImageHeightSetting->value
        . '" value="' . get_option(Settings::xfPostImageHeightSetting->value) . '"'
        . '" type="text" minlength="1" maxlength="10" size="6" />';
    }

    public static function xfImageWidthField()
    {
        echo '<input name="' . Settings::xfPostImageWidthSetting->value
        . '" id="' . Settings::xfPostImageWidthSetting->value
        . '" value="' . get_option(Settings::xfPostImageWidthSetting->value) . '"'
        . '" type="text" minlength="1" maxlength="10" size="6" />';
    }

    public static function setupPostMetaFields()
    {
        add_action('add_meta_boxes_post', static::addPostMetaFields(...));
    }

    /**
     *
     * @param mixed $id
     * @param mixed $title
     * @param mixed $callback
     * @param mixed $page
     * @param string $context
     * @param string $priority
     * @param mixed $callback_args
     * @return void
     */
    public static function addPostMetaFields()
    {
        add_meta_box(
            BridgeInterface::PLUGIN_NAMESPACE . 'bridge_image_sizes',
            esc_html__('Bridged Post Image Settings', 'webixfbridge'),
            static::bridgePostMetaImgSettings(...),
            'post',
            'side',
            'default',
        );
    }

    public static function bridgePostMetaImgSettings($post)
    {
        //wp_nonce_field(BridgeInterface::PLUGIN_NAMESPACE . 'post_meta_nonce', BridgeInterface::SAVE_POST_META);
        echo '<p>
        <label for="'. Settings::xfPostImageWidthSetting->value .'">'. Settings::xfPostImageWidthMetadataText->value .'</label>
        <br />
        <input class="widefat" type="text" name="'. Settings::xfPostImageWidthSetting->value.'" value="'. esc_attr(get_post_meta($post->ID, Settings::xfPostImageWidthSetting->value, true)) .'" size="6">
        </p>
        <p>
        <label for="'. Settings::xfPostImageHeightSetting->value .'">'. Settings::xfPostImageHeightMetadataText->value .'</label>
        <br />
        <input class="widefat" type="text" name="'. Settings::xfPostImageHeightSetting->value.'" value="'. esc_attr(get_post_meta($post->ID, Settings::xfPostImageHeightSetting->value, true)) .'" size="6">
        </p>';
    }
}
