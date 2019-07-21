<?php


namespace App\JsonRpc\Exception;


use Throwable;

class InvalidRequestException extends AbstractJsonRpcException
{
    /**
     * @var string
     */
    private $details;

    public function __construct(string $details)
    {
        parent::__construct();

        $this->details = $details;
    }

    public function getDetails(): string
    {
        return $this->details;
    }
}
