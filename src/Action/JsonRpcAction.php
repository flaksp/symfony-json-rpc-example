<?php

declare(strict_types=1);

namespace App\Action;

use App\JsonRpc\Error;
use App\JsonRpc\Exception\InvalidMethodParametersException;
use App\JsonRpc\Exception\ParseErrorException;
use App\JsonRpc\JsonRpcVersion;
use App\JsonRpc\ProcedureCall;
use App\JsonRpc\ProcedureCallProcessor;
use App\JsonRpc\Response\ErrorResponse;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolation\ConstraintViolationInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
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
            return $this->getParseErrorResponse('Request body is empty');
        }

        try {
            /** @var ProcedureCall $procedureCall */
            $procedureCall = $this->serializer->deserialize(
                $json,
                ProcedureCall::class,
                JsonEncoder::FORMAT,
                [
                    'propertyPath' => [],
                ]
            );
        } catch (NotEncodableValueException $e) {
            return $this->getParseErrorResponse('Could not decode your JSON');
        } catch (DeserializationFailure $e) {
            return $this->getInvalidRequestResponse($e->getConstraintViolations());
        }

        $response = $this->procedureCallProcessor->process($procedureCall);

        if ($procedureCall->isNotification() === true) {
            return $this->getNotificationResponse();
        }

        if ($request->headers->get('Accept') !== 'application/json') {
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
                'Content-Length' => mb_strlen($procedureResponse, '8bit'),
            ]
        );
    }

    private function getParseErrorResponse(string $message): Response
    {
        $responseBody = $this->serializer->serialize(
            new ErrorResponse(
                new JsonRpcVersion('2.0'),
                null,
                new Error(
                    Error::CODE_PARSE_ERROR,
                    $message,
                    null
                )
            ),
            JsonEncoder::FORMAT
        );

        return new Response(
            $responseBody,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json',
                'Content-Length' => mb_strlen($responseBody, '8bit'),
            ]
        );
    }

    /**
     * @param ConstraintViolationInterface[]
     */
    private function getInvalidRequestResponse(array $constraintViolations): Response
    {
        $responseBody = $this->serializer->serialize(
            new ErrorResponse(
                new JsonRpcVersion('2.0'),
                null,
                new Error(
                    Error::CODE_INVALID_REQUEST,
                    'Invalid Request',
                    $constraintViolations
                )
            ),
            JsonEncoder::FORMAT
        );

        return new Response(
            $responseBody,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json',
                'Content-Length' => mb_strlen($responseBody, '8bit'),
            ]
        );
    }

    private function getNotificationResponse(): Response
    {
        return new Response(
            '',
            Response::HTTP_OK,
            []
        );
    }
}
