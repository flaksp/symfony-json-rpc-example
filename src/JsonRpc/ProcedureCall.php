<?php

declare(strict_types=1);

namespace App\JsonRpc;

use InvalidArgumentException;

class ProcedureCall
{
    /**
     * @var int|string|null
     */
    private $id;

    /**
     * @var string
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
     * @param mixed[]         $parameters
     * @param int|string|null $id
     */
    public function __construct(
        JsonRpcVersion $version,
        string $method,
        array $parameters,
        $id
    ) {
        $this->version = $version;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->id = $id;

        if (!is_string($this->id) && !is_int($this->id) && $this->id !== null) {
            throw new InvalidArgumentException(sprintf(
                'Procedure Call ID should be string, number or null, but "%s" type given',
                gettype($this->id)
            ));
        }
    }

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getMethod(): string
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
}
