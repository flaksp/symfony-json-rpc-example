<?php

declare(strict_types=1);

namespace App\JsonRpc\Response;

use App\JsonRpc\JsonRpcCallId;
use App\JsonRpc\JsonRpcVersion;

abstract class AbstractResponse
{
    /**
     * @var JsonRpcCallId|null
     */
    private $id;

    /**
     * @var JsonRpcVersion
     */
    private $version;

    public function __construct(
        JsonRpcVersion $version,
        ?JsonRpcCallId $id
    ) {
        $this->version = $version;
        $this->id = $id;
    }

    public function getId(): ?JsonRpcCallId
    {
        return $this->id;
    }

    public function getVersion(): JsonRpcVersion
    {
        return $this->version;
    }
}
