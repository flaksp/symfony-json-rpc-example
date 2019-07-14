<?php


namespace App\JsonRpc\Response;


class SuccessfulResponse extends AbstractResponse
{
    /**
     * @var mixed
     */
    private $result;

    /**
     * @param string|int|null $id
     * @param mixed $result
     */
    public function __construct(
        string $version,
        $id,
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
