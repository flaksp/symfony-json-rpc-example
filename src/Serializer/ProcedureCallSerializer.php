<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\Exception\InvalidRequestException;
use App\JsonRpc\JsonRpcVersion;
use App\JsonRpc\ProcedureCall;
use App\JsonRpc\ProcedureCallHandler;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolation\MandatoryFieldMissing;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ProcedureCallSerializer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = []): ProcedureCall
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        $violations = [];

        if (array_key_exists('jsonrpc', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                ['jsonrpc']
            );
        } else {
            try {
                $data['jsonrpc'] = $this->denormalizer->denormalize(
                    $data['jsonrpc'],
                    JsonRpcVersion::class,
                    $format,
                    [
                        'propertyPath' => $context['propertyPath'] + ['jsonrpc'],
                    ]
                );
            } catch (DeserializationFailure $e) {
                $violations = $violations + $e->getConstraintViolations();
            }
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

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === ProcedureCallHandler::class && $format === JsonEncoder::FORMAT;
    }
}
