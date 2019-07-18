<?php


namespace App\JsonRpc;


use App\JsonRpc\Exception\MethodNotFoundException;
use App\Message\Sum;

class MethodToMessageMapper
{
    public static function getMessageClassByMethod(string $method): string {
        $mapping = [
            'sum' => Sum::class
        ];

        if (array_key_exists($method, $mapping) === false) {
            throw new MethodNotFoundException();
        }

        return $mapping[$method];
    }
}
