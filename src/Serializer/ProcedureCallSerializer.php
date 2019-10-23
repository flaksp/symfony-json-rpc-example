<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\JsonRpcCallId;
use App\JsonRpc\JsonRpcMethod;
use App\JsonRpc\JsonRpcVersion;
use App\JsonRpc\ProcedureCall;
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
                        'propertyPath' => array_merge($context['propertyPath'], ['jsonrpc']),
                    ]
                );
            } catch (DeserializationFailure $e) {
                $violations = array_merge($violations, $e->getConstraintViolations());
            }
        }

        if (array_key_exists('method', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                ['method']
            );
        } else {
            try {
                $data['method'] = $this->denormalizer->denormalize(
                    $data['method'],
                    JsonRpcMethod::class,
                    $format,
                    [
                        'propertyPath' => array_merge($context['propertyPath'], ['method']),
                    ]
                );
            } catch (DeserializationFailure $e) {
                $violations = array_merge($violations, $e->getConstraintViolations());
            }
        }

        if (array_key_exists('id', $data) === false) {
            $data['id'] = null;
        } else {
            try {
                $data['id'] = $this->denormalizer->denormalize(
                    $data['id'],
                    JsonRpcCallId::class,
                    $format,
                    [
                        'propertyPath' => array_merge($context['propertyPath'], ['id']),
                    ]
                );
            } catch (DeserializationFailure $e) {
                $violations = array_merge($violations, $e->getConstraintViolations());
            }
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
        return $type === ProcedureCall::class && $format === JsonEncoder::FORMAT;
    }
}
