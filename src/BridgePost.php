<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\Settings;
use WebiXfBridge\XFApi\Endpoint;

use function get_post_meta;
use function get_option;

final class BridgePost extends AbstractBridge
{
    protected ?string $imageHeight = null;
    protected ?string $imageWidth  = null;

    protected function init()
    {
        add_action(
            'transition_post_status',
            [$this, 'process'],
            10,
            3
        );
        add_action('load-post.php', SettingsUi::setupPostMetaFields(...));
        add_action('load-post-new.php', SettingsUi::setupPostMetaFields(...));
    }

    public function process(string $newStatus, string $oldStatus, object $post)
    {
        $transCount = did_action('transition_post_status');
        $status     = $post->post_status ?? null;
        // save these now so they are available in the handle method
        parent::savePostMetaFields($post->ID, $post);

        $result = match (true) {
            //($transCount === 1 && $oldStatus === 'draft' && $newStatus === 'publish'),
            ($transCount === 1 && $oldStatus === 'publish' && $newStatus === 'publish') => $this->handlePost($post),
            ($transCount === 1 && $oldStatus === 'publish' && $newStatus === 'trash')   => $this->handleDelete($post),
            /**
             * old = auto-draft | new = draft   | trans count = 1
             * old = new        | new = inherit | trans count = 2
             * old = draft      | new = draft   | trans count = ? 1
             */
            default => $this->initMetaData($post->ID), // this sets the post meta data for the remote thread id and post id to 0 on draft
        };
    }

    protected function handleDelete($post)
    {
        $enableDelete = (bool) get_option(Settings::deleteXfThreadSetting->value);
        if ($enableDelete) {

            $xfThreadId = (string) get_post_meta($post->ID, self::THREAD_ID_COLUMN, true);

            $body = [
                'node_id'       => get_option(Settings::nodeIdSetting->value),
                'hard_delete'   => true,
                'starter_alert' => true,
            ];

            $this->apiRequest
            ->setWpPostId($post->ID)
            ->setHttpMethod(self::HTTP_DELETE_METHOD)
            ->setHeaders($this->getHeaders(self::HTTP_DELETE_METHOD))
            ->setBody($body)
            ->setApiPath(Endpoint::threads->value . $xfThreadId)
            ->makeRequest();
        }
    }

    protected function handlePost($post) // is running for both, switch api location based on meta value of remote,
    {
        $content = $this->getPostContent($post);
        $this->formatter->setImageHeight($this->getImageHeight($post->ID));
        $this->formatter->setImageWidth($this->getImageWidth($post->ID));
        $body = [
            //'node_id' => get_option(Settings::nodeIdSetting->value),
            'title'   => $post->post_title,
            'message' => $this->formatter->format($post, $content, $this->useExcerpt),
        ];
        // set the request state
        $xfPostId = (string) get_post_meta($post->ID, self::POST_ID_COLUMN, true);

        $this->apiRequest
        ->setWpPostId($post->ID)
        ->setHttpMethod(self::HTTP_POST_METHOD)
        ->setHeaders($this->getHeaders(self::HTTP_POST_METHOD));

        if ($xfPostId === '0') { // if the meta data is set to zero then the post needs to be created on the xf side
            $this->apiRequest->setApiPath(Endpoint::threads->value);
            $body['node_id'] = get_option(Settings::nodeIdSetting->value);
        } else { // any other value and we need to update the post
            $this->apiRequest->setApiPath(Endpoint::posts->value . $xfPostId); // set api path to /api/posts/
            $body['silent'] = false;
        }

        $this->apiRequest
        ->setBody($body)
        ->makeRequest();
    }

    private function initMetaData($postId)
    {
        update_metadata(
            'post',
            $postId,
            self::THREAD_ID_COLUMN,
            0,
            true
        );
        update_metadata(
            'post',
            $postId,
            self::POST_ID_COLUMN,
            0,
            true
        );
    }

    public function getImageHeight($postId): string
    {
        $metaHeight        = get_post_meta($postId, Settings::xfPostImageHeightSetting->value, true);
        $fallBackHeight    = get_option(Settings::xfPostImageHeightSetting->value, false);
        $this->imageHeight = (isset($metaHeight) && is_string($metaHeight) && $metaHeight !== '') ? $metaHeight : $fallBackHeight;
        return $this->imageHeight;
    }

    public function getImageWidth($postId): string
    {
        $metaWidth        = get_post_meta($postId, Settings::xfPostImageWidthSetting->value, true);
        $fallBackWidth    = get_option(Settings::xfPostImageWidthSetting->value, false);
        $this->imageWidth = (isset($metaWidth) && is_string($metaWidth) && $metaWidth !== '') ? $metaWidth : $fallBackWidth;
        return $this->imageWidth;
    }
}
