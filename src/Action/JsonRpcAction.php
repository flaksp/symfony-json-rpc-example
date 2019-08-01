<?php

declare(strict_types=1);

namespace App\Action;

use App\JsonRpc\Error;
use App\JsonRpc\JsonRpcVersion;
use App\JsonRpc\ProcedureCall;
use App\JsonRpc\ProcedureCallProcessor;
use App\JsonRpc\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class JsonRpcAction
{
    public const ROUTE = __CLASS__;

    /**
     * @var ProcedureCallProcessor
     */
    private $procedureCallProcessor;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        RequestStack $requestStack,
        SerializerInterface $serializer,
        ProcedureCallProcessor $procedureCallProcessor
    ) {
        $this->requestStack = $requestStack;
        $this->serializer = $serializer;
        $this->procedureCallProcessor = $procedureCallProcessor;
    }

    /**
     * @Route("/jsonrpc", name=JsonRpcAction::ROUTE, methods={"POST"})
     */
    public function __invoke(): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new \RuntimeException('Current request is empty');
        }

        if ($request->headers->get('Content-Type') !== 'application/json') {
            throw new UnsupportedMediaTypeHttpException();
        }

        $json = $request->getContent();

        if ($json === '') {
            return new Response(
                $this->serializer->serialize(
                    new ErrorResponse(
                        new JsonRpcVersion('2.0'),
                        null,
                        new Error(
                            Error::CODE_PARSE_ERROR,
                            'Parse error',
                            null
                        )
                    ),
                    JsonEncoder::FORMAT
                ),
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/json',
                ]
            );
        }

        /** @var ProcedureCall|ProcedureCall[] $procedureCall */
        $procedureCall = $this->serializer->deserialize(
            $json,
            ProcedureCall::class,
            JsonEncoder::FORMAT,
            [
                'propertyPath' => [],
            ]
        );

        $areAllProcedureCallsNotifications = true;

        if (is_array($procedureCall)) {
            $response = $this->procedureCallProcessor->processBatch($procedureCall);

            foreach ($procedureCall as $procedureCallItem) {
                if ($procedureCallItem->isNotification() === false) {
                    $areAllProcedureCallsNotifications = false;

                    break;
                }
            }
        } else {
            $response = $this->procedureCallProcessor->process($procedureCall);

            $areAllProcedureCallsNotifications = $procedureCall->isNotification();
        }

        if ($areAllProcedureCallsNotifications === false && $request->headers->get('Accept') !== 'application/json') {
            throw new NotAcceptableHttpException();
        }

        $procedureResponse = $this->serializer->serialize(
            $response,
            JsonEncoder::FORMAT
        );

        return new Response(
            $procedureResponse,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json',
                'Content-Length' => mb_strlen($procedureResponse),
            ]
        );
    }
}
