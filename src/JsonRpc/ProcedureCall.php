<?php


namespace App\JsonRpc;


use InvalidArgumentException;

class ProcedureCall
{
    /**
     * @var JsonRpcVersion
     */
    private $version;

    /**
     * @var string
     */
    private $method;

    /**
     * @var mixed[]
     */
    private $parameters;

    /**
     * @var string|int|null
     */
    private $id;

    /**
     * @param mixed[] $parameters
     * @param string|int|null $id
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

        if (!is_string($this->id) && !is_int($this->id) && !is_null($this->id)) {
            throw new InvalidArgumentException(sprintf(
                'Procedure Call ID should be string, number or null, but "%s" type given',
                gettype($this->id)
            ));
        }
    }

    public function getVersion(): JsonRpcVersion
    {
        return $this->version;
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

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

}
