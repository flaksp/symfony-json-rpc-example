<?php

declare(strict_types=1);

namespace App\JsonRpc\Response;

use App\JsonRpc\Error;
use App\JsonRpc\JsonRpcCallId;
use App\JsonRpc\JsonRpcVersion;

class ErrorResponse extends AbstractResponse
{
    /**
     * @var Error
     */
    private $error;

    public function __construct(
        JsonRpcVersion $version,
        ?JsonRpcCallId $id,
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
