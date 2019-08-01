<?php

declare(strict_types=1);

namespace App\Tests\Test\Action;

use App\JsonRpc\Error;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class JsonRpcActionTest extends WebTestCase
{
    /**
     * @group jsonrpc
     */
    public function testInvalidNotificationRequest(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'jsonrpc' => '2.0',
                'method' => 'sum',
                'params' => [
                    'a' => [],
                    'b' => 2,
                ],
            ])
        );

        self::assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        self::assertFalse(
            $client->getResponse()->headers->has('Content-Type')
        );

        self::assertEquals(
            '',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group apiproblem
     * @group jsonrpc
     */
    public function testMissingAcceptHeader(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
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

        self::assertEquals(
            Response::HTTP_NOT_ACCEPTABLE,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            'application/problem+json',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'type' => '/api-problem/not-acceptable',
                'title' => 'Not Acceptable',
                'status' => Response::HTTP_NOT_ACCEPTABLE,
                'instance' => null,
                'detail' => 'Server can\'t produce response in format you have defined in Accept header. Maybe you forgot to add that header? Or you have sent wrong value? Check the documentation for supported Content-Type header values that server set for responses for that endpoint or contact developers if you feel problems.',
            ]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group apiproblem
     * @group jsonrpc
     */
    public function testMissingContentTypeHeader(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ],
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

        self::assertEquals(
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            'application/problem+json',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'type' => '/api-problem/unsupported-media-type',
                'title' => 'Unsupported Media Type',
                'status' => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                'instance' => null,
                'detail' => 'Server does not support Content-Type you have sent. If you sent POST, PUT or PATCH request, you should add Content-Type header that describes content in your request body. Maybe you forgot to add that header? Or you have sent wrong value? Check the documentation for supported Content-Type header values for that endpoint or contact developers if you feel problems.',
            ]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group jsonrpc
     */
    public function testParseErrorJsonRpcError(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            ''
        );

        self::assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            'application/json',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'jsonrpc' => '2.0',
                'id' => null,
                'error' => [
                    'code' => Error::CODE_PARSE_ERROR,
                    'message' => 'Parse error',
                ],
            ]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group jsonrpc
     */
    public function testSuccessfulBatchRequest(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                [
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'sum',
                    'params' => [
                        'a' => 1,
                        'b' => 2,
                    ],
                ],
                [
                    'jsonrpc' => '2.0',
                    'id' => 2,
                    'method' => 'sum',
                    'params' => [
                        'a' => 1,
                        'b' => 2,
                    ],
                ],
            ])
        );

        self::assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            'application/json',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertJsonStringEqualsJsonString(
            json_encode([
                [
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'result' => 3,
                ],
                [
                    'jsonrpc' => '2.0',
                    'id' => 2,
                    'result' => 3,
                ],
            ]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group jsonrpc
     */
    public function testSuccessfulBatchRequestRegularAndNotification(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                [
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'sum',
                    'params' => [
                        'a' => 1,
                        'b' => 2,
                    ],
                ],
                [
                    'jsonrpc' => '2.0',
                    'method' => 'sum',
                    'params' => [
                        'a' => 1,
                        'b' => 2,
                    ],
                ],
            ])
        );

        self::assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            'application/json',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertJsonStringEqualsJsonString(
            json_encode([
                [
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'result' => 3,
                ],
            ]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group jsonrpc
     */
    public function testSuccessfulNotificationBatchRequest(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                [
                    'jsonrpc' => '2.0',
                    'method' => 'sum',
                    'params' => [
                        'a' => 1,
                        'b' => 2,
                    ],
                ],
                [
                    'jsonrpc' => '2.0',
                    'method' => 'sum',
                    'params' => [
                        'a' => 1,
                        'b' => 2,
                    ],
                ],
            ])
        );

        self::assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        self::assertFalse(
            $client->getResponse()->headers->has('Content-Type')
        );

        self::assertEquals(
            '',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group jsonrpc
     */
    public function testSuccessfulNotificationRequest(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'jsonrpc' => '2.0',
                'method' => 'sum',
                'params' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ])
        );

        self::assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        self::assertFalse(
            $client->getResponse()->headers->has('Content-Type')
        );

        self::assertEquals(
            '',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group jsonrpc
     */
    public function testSuccessfulRequest(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
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

        self::assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            'application/json',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'jsonrpc' => '2.0',
                'id' => 1,
                'result' => 3,
            ]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group apiproblem
     * @group jsonrpc
     */
    public function testWrongAcceptHeader(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'text/plain',
            ],
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

        self::assertEquals(
            Response::HTTP_NOT_ACCEPTABLE,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            'application/problem+json',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'type' => '/api-problem/not-acceptable',
                'title' => 'Not Acceptable',
                'status' => Response::HTTP_NOT_ACCEPTABLE,
                'instance' => null,
                'detail' => 'Server can\'t produce response in format you have defined in Accept header. Maybe you forgot to add that header? Or you have sent wrong value? Check the documentation for supported Content-Type header values that server set for responses for that endpoint or contact developers if you feel problems.',
            ]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @group apiproblem
     * @group jsonrpc
     */
    public function testWrongContentTypeHeader(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/jsonrpc',
            [],
            [],
            [
                'CONTENT_TYPE' => 'text/plain',
                'HTTP_ACCEPT' => 'application/json',
            ],
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

        self::assertEquals(
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            $client->getResponse()->getStatusCode()
        );

        self::assertEquals(
            'application/problem+json',
            $client->getResponse()->headers->get('Content-Type')
        );

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'type' => '/api-problem/unsupported-media-type',
                'title' => 'Unsupported Media Type',
                'status' => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                'instance' => null,
                'detail' => 'Server does not support Content-Type you have sent. If you sent POST, PUT or PATCH request, you should add Content-Type header that describes content in your request body. Maybe you forgot to add that header? Or you have sent wrong value? Check the documentation for supported Content-Type header values for that endpoint or contact developers if you feel problems.',
            ]),
            $client->getResponse()->getContent()
        );
    }
}
