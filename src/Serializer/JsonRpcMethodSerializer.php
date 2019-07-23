<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\JsonRpcMethod;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolation\ConstraintViolationInterface;
use App\Validator\ConstraintViolation\ValueIsNotValid;
use App\Validator\ConstraintViolation\WrongPropertyType;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JsonRpcMethodSerializer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = []): JsonRpcMethod
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        /** @var ConstraintViolationInterface[] $violations */
        $violations = [];

        if (is_string($data) === false) {
            $violations[] = new WrongPropertyType(
                $context['propertyPath'],
                gettype($data),
                ['string']
            );
        } else {
            if (JsonRpcMethod::getMessageClassByMethodName($data) === null) {
                $violations[] = new ValueIsNotValid(
                    $context['propertyPath'],
                    'JSON RPC method with given name does not exist.'
                );
            }
        }

        if (count($violations) > 0) {
            throw new DeserializationFailure($violations);
        }

        return new JsonRpcMethod($data);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === JsonRpcMethod::class;
    }
}
