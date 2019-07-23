<?php

declare(strict_types=1);

namespace App\JsonRpc;

class JsonRpcCallId
{
    /**
     * @var int|string
     */
    private $identifier;

    /**
     * @param int|string $identifier
     */
    public function __construct(
        $identifier
    ) {
        $this->identifier = $identifier;

        if (!is_int($this->identifier) && !is_string($this->identifier)) {
            throw new \InvalidArgumentException('Identifier should be string or integer');
        }
    }

    /**
     * @return int|string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
