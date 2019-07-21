<?php

declare(strict_types=1);

namespace App\JsonRpc\Response;

abstract class AbstractResponse
{
    /**
     * @var int|string|null
     */
    private $id;

    /**
     * @var string
     */
    private $version;

    /**
     * @param int|string|null $id
     */
    public function __construct(
        string $version,
        $id
    ) {
        $this->version = $version;
        $this->id = $id;
    }

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
