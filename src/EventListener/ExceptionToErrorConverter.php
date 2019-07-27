<?php

declare(strict_types=1);

namespace App\EventListener;

use App\ValueObject\ApiProblem\ApiProblem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionToErrorConverter
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(new Response(
                $this->serializer->serialize(ApiProblem::createNotFoundApiProblem(), JsonEncoder::FORMAT),
                Response::HTTP_NOT_FOUND,
                [
                    'Content-Type' => 'application/problem+json',
                ]
            ));

            return;
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            $event->setResponse(new Response(
                $this->serializer->serialize(ApiProblem::createMethodNotAllowedApiProblem(), JsonEncoder::FORMAT),
                Response::HTTP_METHOD_NOT_ALLOWED,
                [
                    'Content-Type' => 'application/problem+json',
                ]
            ));

            return;
        }

        if ($exception instanceof ServiceUnavailableHttpException) {
            $event->setResponse(new Response(
                $this->serializer->serialize(ApiProblem::createServiceUnavailableApiProblem(), JsonEncoder::FORMAT),
                Response::HTTP_SERVICE_UNAVAILABLE,
                [
                    'Content-Type' => 'application/problem+json',
                    'Retry-After' => '60',
                ]
            ));

            return;
        }

        if ($exception instanceof UnsupportedMediaTypeHttpException) {
            $event->setResponse(new Response(
                $this->serializer->serialize(ApiProblem::createUnsupportedMediaTypeApiProblem(), JsonEncoder::FORMAT),
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                [
                    'Content-Type' => 'application/problem+json',
                ]
            ));

            return;
        }

        if ($exception instanceof NotAcceptableHttpException) {
            $event->setResponse(new Response(
                $this->serializer->serialize(ApiProblem::createNotAcceptableApiProblem(), JsonEncoder::FORMAT),
                Response::HTTP_NOT_ACCEPTABLE,
                [
                    'Content-Type' => 'application/problem+json',
                ]
            ));

            return;
        }
    }
}
