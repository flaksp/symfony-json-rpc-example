<?php

declare(strict_types=1);

namespace App\ValueObject\ApiProblem;

use Symfony\Component\HttpFoundation\Response;

class ApiProblem
{
    /**
     * @var string
     */
    private $detail;

    /**
     * @var string|null
     */
    private $instance;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $type;

    public function __construct(
        int $status,
        string $type,
        string $title,
        string $detail,
        ?string $instance = null
    ) {
        $this->status = $status;
        $this->type = $type;
        $this->title = $title;
        $this->detail = $detail;
        $this->instance = $instance;
    }

    public static function createMethodNotAllowedApiProblem(): self
    {
        return new self(
            Response::HTTP_METHOD_NOT_ALLOWED,
            '/api-problem/method-not-allowed',
            'Method Not Allowed',
            'URL you have requested does not support given HTTP method. Maybe you have sent POST, PUT, PATCH or DELETE request to some endpoint using "http://" scheme (API works only over HTTPS)? Maybe you have requested deprecated endpoint? Please take a look at the documentation and then try to ask developers about that.'
        );
    }

    public static function createNotAcceptableApiProblem(): self
    {
        return new self(
            Response::HTTP_NOT_ACCEPTABLE,
            '/api-problem/not-acceptable',
            'Not Acceptable',
            'Server can\'t produce response in format you have defined in Accept header. Maybe you forgot to add that header? Or you have sent wrong value? Check the documentation for supported Content-Type header values that server set for responses for that endpoint or contact developers if you feel problems.'
        );
    }

    public static function createNotFoundApiProblem(): self
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            '/api-problem/not-found',
            'Not Found',
            'Endpoint you are looking for not found. Maybe you are using wrong HTTP method? Maybe you have requested deprecated endpoint? Maybe your URL contains trailing slash? Please take a look at the documentation and then try to ask developers about that.'
        );
    }

    public static function createServiceUnavailableApiProblem(): self
    {
        return new self(
            Response::HTTP_SERVICE_UNAVAILABLE,
            '/api-problem/service-unavailable',
            'Service Unavailable',
            'Seems like some internal service is unavailable right now. Check GET /health endpoint for details.'
        );
    }

    public static function createUnsupportedMediaTypeApiProblem(): self
    {
        return new self(
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            '/api-problem/unsupported-media-type',
            'Unsupported Media Type',
            'Server does not support Content-Type you have sent. If you sent POST, PUT or PATCH request, you should add Content-Type header that describes content in your request body. Maybe you forgot to add that header? Or you have sent wrong value? Check the documentation for supported Content-Type header values for that endpoint or contact developers if you feel problems.'
        );
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function getInstance(): ?string
    {
        return $this->instance;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
