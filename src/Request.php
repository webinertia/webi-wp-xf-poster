<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;
use WebiXfBridge\Headers\XFApiKeyHeader;
use WebiXfBridge\Headers\XFUserHeader;
use WebiXfBridge\XFApi\Response\ResponseHandler;
use WebiXfBridge\Settings;
use WebiXfBridge\XFApi\Response\DeleteResponse;
use WebiXfBridge\XFApi\Response\PostResponse;

use function array_merge;
use function get_option;
use function in_array;

final class Request
{
    private ?string $apiPath;
    private ?string $apiUrl;
    //private ?string $apiKey;
    private string $httpMethod = BridgeInterface::HTTP_GET_METHOD;
    /** @var array<TKEY, TVALUE> $body */
    private array   $body = [];
    //private ?string $xfUserId;
    private ?string $nodeId;
    /** set the const default */
    private ?string $httpVersion = BridgeInterface::HTTP_VERSION;
    /** @var array<TKEY, TVALUE> $headers */
    private array   $headers = [];
    private int $wpPostId;

    public function __construct(
        private $userIdHeader = new XFUserHeader(),
        private $apiKeyHeader = new XFApiKeyHeader(),
    ) {
        $this->setApiUrl(get_option(Settings::apiUrlSetting->value));
        $this->setNodeId(get_option(Settings::xfUserIdSetting->value));
    }

    public function makeRequest(?string $apiPath = null, array $headers = [], array $body = [])
    {
        $payload  = [
            'method'      => $this->httpMethod,
            'httpversion' => $this->httpVersion,
            'blocking'    => true,
            'headers'     => $this->headers, // prefer headers passed as method arguments
            'body'        => array_merge($this->body, $body), // prefer body passed as method arguments
        ];
        $this->apiUrl .= $apiPath ?? $this->apiPath;
        // make api request to store data on the xf forum
        $response = wp_remote_post(
            $this->apiUrl,
            $payload
        );
        // handle the responses
        $handler = new ResponseHandler();
        $handled = match ($this->httpMethod) {
            BridgeInterface::HTTP_POST_METHOD   => $handler->handlePostResponse(new PostResponse($response), $this->wpPostId),
            BridgeInterface::HTTP_DELETE_METHOD => $handler->handleDeleteResponse(new DeleteResponse($response), $this->wpPostId),
            default                             => throw new Exception\InvalidLogicException('Unknown Http Method'),
        };
    }

    public function setHttpMethod(string $httpMethod): self
    {
        if (! in_array($httpMethod, BridgeInterface::ALLOWED_HTTP_METHODS))
        {
            throw new Exception\InvalidArgumentException("Http method: $httpMethod not allowed.");
        }
        $this->httpMethod = $httpMethod;
        return $this;
    }

    public function setBody(array $body): self
    {
        // maintain all previous keys, while resetting any incoming keys
        $this->body = array_merge($this->body, $body);
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setNodeId(string $nodeId): self
    {
        $this->nodeId = $nodeId;
        return $this;
    }

    public function getNodeId(): string
    {
        return $this->nodeId;
    }

    public function setApiPath(string $apiPath): self
    {
        $this->apiPath = $apiPath;
        return $this;
    }

    public function getApiPath(): string|null
    {
        return $this->apiPath;
    }

    public function setApiUrl(string $apiUrl): self
    {
        $this->apiUrl = $apiUrl;
        return $this;
    }

    public function getApiUrl(): string|null
    {
        return $this->apiUrl;
    }

    public function setWpPostId(int $wpPostId): self
    {
        $this->wpPostId = $wpPostId;
        return $this;
    }

    public function getWpPostId(): int
    {
        return $this->wpPostId;
    }
}