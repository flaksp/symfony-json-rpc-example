<?php

declare(strict_types=1);

namespace App\EventListener;

use App\JsonRpc\Error;
use App\JsonRpc\Response\ErrorResponse;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionToInternalServerErrorConverter
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        LoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $event->setResponse(new Response(
            $this->serializer->serialize(new ErrorResponse(
                '2.0',
                null,
                new Error(
                    Error::CODE_INTERNAL_ERROR,
                    'Internal error',
                    null
                )
            ), JsonEncoder::FORMAT),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            [
                'Content-Type' => 'application/json',
            ]
        ));

        $this->logException($event->getException());
    }

    private function logException(Exception $exception): void
    {
        $message = sprintf(
            'Uncaught PHP Exception %s: "%s" at %s line %s',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        $context = ['exception' => $exception];

        $this->logger->critical($message, $context);
    }
}
