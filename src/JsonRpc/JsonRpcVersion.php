<?php


namespace App\JsonRpc;


class JsonRpcVersion
{
    /**
     * @var string
     */
    private $version;

    public function __construct(
        string $version
    ) {
        $this->version = $version;

        if ($this->version !== '2.0') {
            throw new \InvalidArgumentException('Only JSON RPC version 2.0 is supported');
        }
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
