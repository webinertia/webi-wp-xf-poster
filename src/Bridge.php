<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;
use WebiXfBridge\Settings;
use WebiXfBridge\Exception\InvalidLogicException;
use WebiXfBridge\Headers\XFApiKeyHeader;
use WebiXfBridge\Headers\XFUserHeader;
use WebiXfBridge\XFApi\Endpoint;

use function array_merge;
use function did_action;
use function get_metadata;
use function get_option;
use function wp_strip_all_tags;

class Bridge implements BridgeInterface
{
    public function __construct(
    ) {
    }

    public function handleTransition($old, $new, $post)
    {
        // DO NOT CHANGE THIS WITHOUT EXTENSIVE TESTING !!!!!!!!!!!!!!!
        $transCount = did_action('transition_post_status');
        if ($transCount === 1) {
            $request = new Request();
        }
        // save new condition
        if ($old === 'publish' && $new === 'auto-draft' && $transCount === 1) { // save new pages
            $headers = array_merge(
                ['Content-type' => self::POST_HEADER_TYPE],
                (new XFUserHeader())->getHeader(),
                (new XFApiKeyHeader())->getHeader(),
            );
            $body = [
                'node_id' => get_option(Settings::nodeIdSetting->value),
                'title'   => $post->post_title,
                'message' => wp_strip_all_tags($post->post_content),
            ];
            // set the request state
            $request
            ->setAPiPath(Endpoint::threads->value)
            ->setWpPostId($post->ID)
            ->setHttpMethod(self::HTTP_POST_METHOD) // set method to POST
            ->setAPiPath(Endpoint::threads->value) // set api path to /api/threads/
            ->setHeaders($headers)
            ->setBody($body)
            ->makeRequest();
            return;
        }
        // edit condition
        if ($old === 'publish' && $new === 'publish' && $transCount === 1) {
           $headers = array_merge(
                ['Content-type' => self::POST_HEADER_TYPE],
                (new XFUserHeader())->getHeader(),
                (new XFApiKeyHeader())->getHeader(),
            );
            $body = [
                'message' => wp_strip_all_tags($post->post_content),
                'silent'  => false,
                'author_alert' => true,
            ];
            // get the post id from the metadata
            $targetPostId = get_metadata('post', $post->ID, BridgeInterface::POST_ID_COLUMN, true);
            if (! $targetPostId) {
                throw new InvalidLogicException('Post metadata could not be located.');
            }
            // set request state
            $request
            ->setAPiPath(Endpoint::posts->value . (int) $targetPostId) // /post/
            ->setWpPostId($post->ID)
            ->setHttpMethod(self::HTTP_POST_METHOD) // set method to POST
            ->setHeaders($headers)
            ->setBody($body)
            ->makeRequest();
            return;
        }
    }

    public function updateHandler($post): void
    {
        if ($post->post_type !== 'post') {
            return;
        }
    }
}