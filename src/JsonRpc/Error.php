<?php

declare(strict_types=1);

namespace App\JsonRpc;

class Error
{
    public const CODE_INTERNAL_ERROR = -32603;
    public const CODE_INVALID_PARAMS = -32602;
    public const CODE_INVALID_REQUEST = -32600;
    public const CODE_PARSE_ERROR = -32700;

    /**
     * @var int
     */
    private $code;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $message;

    /**
     * @param mixed $data
     */
    public function __construct(
        int $code,
        string $message,
        $data
    ) {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
