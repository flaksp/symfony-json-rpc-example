<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\JsonRpcCallId;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolation\ConstraintViolationInterface;
use App\Validator\ConstraintViolation\WrongPropertyType;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JsonRpcCallIdSerializer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = []): JsonRpcCallId
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        /** @var ConstraintViolationInterface[] $violations */
        $violations = [];

        if (is_string($data) === false && is_int($data) === false) {
            $violations[] = new WrongPropertyType(
                $context['propertyPath'],
                gettype($data),
                ['string', 'integer']
            );
        }

        if (count($violations) > 0) {
            throw new DeserializationFailure($violations);
        }

        return new JsonRpcCallId($data);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === JsonRpcCallId::class;
    }
}
