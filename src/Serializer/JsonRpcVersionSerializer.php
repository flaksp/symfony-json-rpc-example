<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\Exception\InvalidRequestException;
use App\JsonRpc\JsonRpcVersion;
use App\Validator\ConstraintViolation\MandatoryFieldMissing;
use App\JsonRpc\ProcedureCall;
use App\JsonRpc\ProcedureCallHandler;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolation\ValueIsNotValid;
use App\Validator\ConstraintViolation\WrongPropertyType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JsonRpcVersionSerializer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    public function denormalize($data, $class, $format = null, array $context = []): JsonRpcVersion
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        if (is_string($data) === false) {
            $violations[] = new WrongPropertyType(
                $context['propertyPath'],
                gettype($data),
                ['string']
            );
        }

        if ($data !== '2.0') {
            $violations[] = new ValueIsNotValid(
                $context['propertyPath'],
                'JSON RPC version should be explicitly specified as described in specification: https://www.jsonrpc.org/specification. At this moment server supports only version "2.0".'
            );
        }

        if (count($violations) > 0) {
            throw new DeserializationFailure($violations);
        }

        return new JsonRpcVersion($data);
    }

    private function validate($data): void
    {
        $violations = [];

        if (array_key_exists('jsonrpc', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                ['jsonrpc']
            );
        } else {

        }

        if ($data['jsonrpc'] !== '2.0') {
            throw new InvalidRequestException(
                'Field "jsonrpc" should contain JSON RPC specification version number. Server supports only version "2.0".'
            );
        }

        if (array_key_exists('method', $data) === false) {
            throw new InvalidRequestException(
                'Your request missing mandatory field "method".'
            );
        }

        if (is_string($data['method']) === false) {
            throw new InvalidRequestException(sprintf(
                'Field "method" should contain string. You have send "%s" type in that field.',
                gettype($data['jsonrpc'])
            ));
        }

        if (array_key_exists('id', $data) === false) {
            throw new InvalidRequestException(
                'Your request missing mandatory field "id".'
            );
        }

        if (array_key_exists('params', $data) === false) {
            $data['params'] = [];
        }


    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === JsonRpcVersion::class;
    }
}
