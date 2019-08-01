<?php

declare(strict_types=1);

namespace App\JsonRpc;

class ProcedureCall
{
    /**
     * @var JsonRpcCallId|null
     */
    private $id;

    /**
     * @var JsonRpcMethod
     */
    private $method;

    /**
     * @var mixed[]
     */
    private $parameters;

    /**
     * @var JsonRpcVersion
     */
    private $version;

    /**
     * @param mixed[] $parameters
     */
    public function __construct(
        JsonRpcVersion $version,
        JsonRpcMethod $method,
        array $parameters,
        ?JsonRpcCallId $id
    ) {
        $this->version = $version;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->id = $id;
    }

    public function getId(): ?JsonRpcCallId
    {
        return $this->id;
    }

    public function getMethod(): JsonRpcMethod
    {
        return $this->method;
    }

    /**
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getVersion(): JsonRpcVersion
    {
        return $this->version;
    }

    public function isNotification(): bool
    {
        return $this->getId() === null;
    }
}
