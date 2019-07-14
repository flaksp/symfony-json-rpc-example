<?php


namespace App\JsonRpc;


class Error
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @var mixed
     */
    private $data;

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

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
