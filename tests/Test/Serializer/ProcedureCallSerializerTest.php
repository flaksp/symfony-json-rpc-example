<?php

declare(strict_types=1);

namespace App\Tests\Test\Serializer;

use App\JsonRpc\ProcedureCall;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ProcedureCallSerializerTest extends KernelTestCase
{
    /**
     * @group jsonrpc
     */
    public function testSuccessfulDenormalization(): void
    {
        static::bootKernel();
        $container = self::$container;

        $serializer = $container->get(SerializerInterface::class);

        $procedureCall = $serializer->deserialize(json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'sum',
            'params' => [
                'a' => 1,
                'b' => 2,
            ],
        ]), ProcedureCall::class, JsonEncoder::FORMAT, [
            'propertyPath' => [],
        ]);

        self::assertInstanceOf(ProcedureCall::class, $procedureCall);
    }
}
