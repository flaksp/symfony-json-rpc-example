<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\JsonRpcVersion;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolation\ConstraintViolationInterface;
use App\Validator\ConstraintViolation\ValueIsNotValid;
use App\Validator\ConstraintViolation\WrongPropertyType;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JsonRpcVersionSerializer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface, NormalizerInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = []): JsonRpcVersion
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

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    /**
     * @param JsonRpcVersion $object
     * @param mixed|null     $format
     */
    public function normalize($object, $format = null, array $context = []): string
    {
        if ($this->supportsNormalization($object, $format) === false) {
            throw new InvalidArgumentException();
        }

        return $object->getVersion();
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === JsonRpcVersion::class;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof JsonRpcVersion;
    }
}
