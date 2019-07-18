<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Validator\ConstraintViolation\MandatoryFieldMissing;
use App\JsonRpc\ProcedureCall;
use App\JsonRpc\ProcedureCallHandler;
use App\Serializer\Exception\DeserializationFailure;
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

class ProcedureCallSerializer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    public function denormalize($data, $class, $format = null, array $context = []): ProcedureCall
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        $violations = [];

        if (array_key_exists('jsonrpc', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                'jsonrpc'
            );
        }

        if (array_key_exists('method', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                'method'
            );
        }

        if (array_key_exists('id', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                'id'
            );
        }

        if (array_key_exists('params', $data) === false) {
            $data['params'] = [];
        }

        if (count($violations) > 0) {
            throw new DeserializationFailure($violations);
        }

        return new ProcedureCall(
            $data['jsonrpc'],
            $data['method'],
            $data['params'],
            $data['id']
        );
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === ProcedureCallHandler::class && $format === JsonEncoder::FORMAT;
    }
}
