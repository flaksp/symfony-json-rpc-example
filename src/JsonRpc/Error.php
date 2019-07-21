<?php

declare(strict_types=1);

namespace App\JsonRpc;

class Error
{
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
