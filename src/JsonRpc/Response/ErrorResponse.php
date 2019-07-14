<?php


namespace App\JsonRpc\Response;


use App\JsonRpc\Error;

class ErrorResponse extends AbstractResponse
{
    /**
     * @var Error
     */
    private $error;

    /**
     * @param string|int|null $id
     */
    public function __construct(
        string $version,
        $id,
        Error $error
    ) {
        $this->error = $error;

        parent::__construct($version, $id);
    }

    public function getError(): Error
    {
        return $this->error;
    }
}
