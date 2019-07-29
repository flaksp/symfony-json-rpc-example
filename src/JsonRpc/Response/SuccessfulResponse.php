<?php

declare(strict_types=1);

namespace App\JsonRpc\Response;

use App\JsonRpc\JsonRpcCallId;
use App\JsonRpc\JsonRpcVersion;

class SuccessfulResponse extends AbstractResponse
{
    /**
     * @var mixed
     */
    private $result;

    /**
     * @param mixed $result
     */
    public function __construct(
        JsonRpcVersion $version,
        ?JsonRpcCallId $id,
        $result
    ) {
        $this->result = $result;

        parent::__construct($version, $id);
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
