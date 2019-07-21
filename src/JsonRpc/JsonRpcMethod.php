<?php


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

        if (self::getMessageClassByMethodName($this) === null) {
            throw new \InvalidArgumentException('Method does not exist');
        }
    }

    public static function getMessageClassByMethodName(
        JsonRpcMethod $method
    ): ?string {
        $mapping = [
            'sum' => Sum::class
        ];

        return $mapping[$method->getName()] ?? null;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
