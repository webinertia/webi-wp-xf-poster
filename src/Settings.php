<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;

enum Settings: string
{
    case DomainKeyName = BridgeInterface::PLUGIN_NAMESPACE . 'domain';
    case ApiKeyName    = BridgeInterface::PLUGIN_NAMESPACE . 'api_key';
    case NodeId        = BridgeInterface::PLUGIN_NAMESPACE . 'node_id';
    case XfUserId      = BridgeInterface::PLUGIN_NAMESPACE . 'xf_user_id';
}
