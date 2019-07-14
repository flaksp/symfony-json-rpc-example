<?php


namespace App\JsonRpc\Response;


class BatchResponse
{
    /**
     * @var AbstractResponse[]
     */
    private $responses;

    /**
     * @param AbstractResponse[] $responses
     */
    public function __construct(
        array $responses
    ) {
        $this->responses = $responses;
    }

    /**
     * @return AbstractResponse[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }
}
