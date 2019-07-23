<?php

declare(strict_types=1);

namespace App\JsonRpc;

use App\Message\Sum;

class JsonRpcMethod
{
    /**
     * @var string
     */
    private $name;

    public function __construct(
        string $name
    ) {
        $this->name = $name;

        if (self::getMessageClassByMethodName($name) === null) {
            throw new \InvalidArgumentException('Method does not exist');
        }
    }

    public static function getMessageClassByMethodName(
        string $method
    ): ?string {
        $mapping = [
            'sum' => Sum::class,
        ];

        return $mapping[$method] ?? null;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
