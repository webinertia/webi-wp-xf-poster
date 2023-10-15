<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;
use WebiXfBridge\Exception\InvalidArgumentException;
use WebiXfBridge\Formatter;
use WebiXfBridge\Settings;
use WebiXfBridge\Headers\XFApiKeyHeader;
use WebiXfBridge\Headers\XFUserHeader;

use function add_post_meta;
use function array_key_exists;
use function current_user_can;
use function delete_post_meta;
use function get_option;
use function get_post_type_object;
use function strtolower;
use function strtoupper;
use function update_post_meta;

class AbstractBridge implements BridgeInterface
{
    protected array $status = [ // here for future use
        'new', 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'
    ];

    public function __construct(
        protected $formatter            = new Formatter(),
        protected $apiRequest           = new Request(),
        protected bool|null $useExcerpt = null
    ) {
        $this->useExcerpt = (bool) get_option(Settings::postExcerptSetting->value);
        $this->init();
    }

    protected function init()
    {
        // initialization hook
    }

    protected function getPostContent($post): string
    {
        $content = '';
        if ($this->useExcerpt && isset($post->post_excerpt) && $post->post_excerpt !== '') {
            $content .= $post->post_excerpt;
        } elseif ($this->useExcerpt && !isset($post->post_excerpt) || $post->post_excerpt === '') {
            $content .= $this->buildExcerpt($post->post_content);
        } else {
            $content .= $post->post_content ?? '';
        }
        return $content;
    }

    public function getHeaders(string $method = 'post'): array
    {
        $method  = strtolower($method);
        $headers = [
            'post'   => array_merge(
                ['Content-type' => self::POST_HEADER_TYPE],
                (new XFUserHeader())->getHeader(),
                (new XFApiKeyHeader())->getHeader()
            ),
            'get'    => [],
            'delete' => array_merge(
                ['Content-type' => self::DELETE_HEADER_TYPE],
                (new XFUserHeader())->getHeader(),
                (new XFApiKeyHeader())->getHeader()
            ),
        ];

        if (! array_key_exists($method, $headers)) {
            throw new InvalidArgumentException('Method: ' . strtoupper($method) . ' is not supported.');
        }
        return $headers[$method];
    }

    protected function buildExcerpt(string $text)
    {
        $excerptLength = (int) get_option(Settings::excerptWordCountSetting->value);
        $words         = str_word_count(strip_tags($text), 1, '.,');
        $excerpt       = implode(' ', array_slice($words, 0, $excerptLength));
        return $excerpt;
    }

    public static function savePostMetaFields($postId, $post)
    {
        // if (
        //     ! isset($_POST[BridgeInterface::PLUGIN_NAMESPACE . 'post_meta_nonce'])
        //     || ! wp_verify_nonce($_POST[BridgeInterface::PLUGIN_NAMESPACE . 'post_meta_nonce'], BridgeInterface::SAVE_POST_META)
        // ) {
        //     return $postId;
        // }
        $postType = get_post_type_object($post->post_type);
        if(! current_user_can($postType->cap->edit_post, $postId)) {
            return $postId;
        }

        $newWidthValue      = $_POST[Settings::xfPostImageWidthSetting->value] ?? null;
        $currentWidthValue  = get_post_meta($postId, Settings::xfPostImageWidthSetting->value, true);
        $newHeightValue     = $_POST[Settings::xfPostImageHeightSetting->value] ?? null;
        $currentHeightValue = get_post_meta($postId, Settings::xfPostImageHeightSetting->value);

        if ($newWidthValue && ! $currentWidthValue) {
            add_post_meta($postId, Settings::xfPostImageWidthSetting->value, $newWidthValue);
        } elseif ($newWidthValue && $newWidthValue != $currentWidthValue) {
            update_post_meta($postId, Settings::xfPostImageWidthSetting->value, $newWidthValue);
        } elseif ($currentWidthValue && ! $newWidthValue) {
            delete_post_meta($postId, Settings::xfPostImageWidthSetting->value, $currentWidthValue);
        }

        if ($newHeightValue && ! $currentHeightValue) {
            add_post_meta($postId, Settings::xfPostImageHeightSetting->value, $newHeightValue);
        } elseif ($newHeightValue && $newHeightValue != $currentHeightValue) {
            update_post_meta($postId, Settings::xfPostImageHeightSetting->value, $newHeightValue);
        } elseif ($currentHeightValue && ! $newHeightValue) {
            delete_post_meta($postId, Settings::xfPostImageHeightSetting->value, $currentHeightValue);
        }
    }
}