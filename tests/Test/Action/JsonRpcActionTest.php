<?php

declare(strict_types=1);

namespace App\Tests\Test\Action;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonRpcActionTest extends WebTestCase
{
    public function testSuccessfulRequest(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [
                'HTTP_Content-Type' => 'application/json',
                'HTTP_Accept' => 'application/json',
            ],
            [],
            json_encode([
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'sum',
                'params' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
