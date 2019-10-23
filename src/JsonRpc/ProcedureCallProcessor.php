<?php

declare(strict_types=1);

namespace App\JsonRpc;

use App\JsonRpc\Response\AbstractResponse;
use App\JsonRpc\Response\ErrorResponse;
use App\JsonRpc\Response\SuccessfulResponse;
use App\Serializer\Exception\DeserializationFailure;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ProcedureCallProcessor
{
    use HandleTrait;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        MessageBusInterface $messageBus,
        SerializerInterface $serializer
    ) {
        $this->messageBus = $messageBus;
        $this->serializer = $serializer;
    }

    public function process(
        ProcedureCall $procedureCall
    ): AbstractResponse {
        try {
            $result = $this->handle(
                $this->serializer->deserialize(
                    json_encode($procedureCall->getParameters()),
                    JsonRpcMethod::getMessageClassByMethodName($procedureCall->getMethod()->getName()),
                    JsonEncoder::FORMAT,
                    [
                        'propertyPath' => ['params'],
                    ]
                    )
                );

            $response = new SuccessfulResponse(
                $procedureCall->getVersion(),
                $procedureCall->getId(),
                $result
            );
        } catch (DeserializationFailure $e) {
            $response = new ErrorResponse(
                $procedureCall->getVersion(),
                $procedureCall->getId(),
                new Error(
                    Error::CODE_INVALID_PARAMS,
                    'Invalid method parameters',
                    $e->getConstraintViolations()
                )
            );
        }

        return $response;
    }
}
