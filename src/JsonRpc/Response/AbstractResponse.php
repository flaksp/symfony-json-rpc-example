<?php


namespace App\JsonRpc\Response;


abstract class AbstractResponse
{
    /**
     * @var string
     */
    private $version;

    /**
     * @var int|string|null
     */
    private $id;

    /**
     * @param string|int|null $id
     */
    public function __construct(
        string $version,
        $id
    ) {
        $this->version = $version;
        $this->id = $id;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }
}
