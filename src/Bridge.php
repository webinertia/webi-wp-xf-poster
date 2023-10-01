<?php

declare(strict_types=1);

namespace WebiXfBridge;

use stdClass;

class Bridge
{
    public const POST_META_KEY  = 'webi_xf_post_id';
    private const ACTION_STATUS = [
        'new', 'publish',
    ];

    private ?stdClass $xfThread;

    public function __construct()
    {

    }

    public function actionHandler(string $newStatus, string $oldStatus, object $post): void
    {
        switch(current_action()) {
            case $oldStatus === 'new' && $newStatus === 'publish': // maybe save
                $this->publishThread($post);
                break;
            case $oldStatus === 'publish' && $newStatus === 'publish': // probably edit so update it but check the meta first

                break;
            case $oldStatus === 'publish' && $newStatus === 'private': // soft delete ie hide thread on xf
                break;
            default:
                // do nothing if we can not determine what is happening
                break;
        }
    }

    private function publishThread(object $post): void
    {
        $xfUserId = '1';
        $apiKey   = 'LGoRoI2XnAlyprffIU_wSLnq9Iv5EPwo';
        $nodeId   = '2';
        $request  = [
            'method' => 'POST',
            'httpversion' => '1.1',
            'blocking' => true,
            'headers'  => [
                'XF-Api-User' => $xfUserId,
                'XF-Api-Key'  => $apiKey,
                'Content-type: application/x-www-form-urlencoded',
            ],
            'body' => [
                'node_id' => $nodeId,
                'title'   => $post->post_title,
                'message' => wp_strip_all_tags($post->post_content),
            ],
        ];
        // make api request to store data on the xf forum
        $response = wp_remote_post('http://xf.local/api/threads', $request);
        // get the thread data from the response body
        $this->xfThread = (json_decode(wp_remote_retrieve_body($response)))->thread;
        // save the xf threads first post id so it can be used to edit later
        update_post_meta($post->ID, self::POST_META_KEY, $this->xfThread->first_post_id);
    }

    public function editFirstPost()
    {

    }
}