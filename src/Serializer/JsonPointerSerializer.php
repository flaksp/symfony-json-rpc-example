<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Validator\ConstraintViolation\ConstraintViolationInterface;
use App\Validator\ConstraintViolation\MandatoryFieldMissing;
use App\JsonRpc\ProcedureCall;
use App\JsonRpc\ProcedureCallHandler;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolationParameter;
use App\Validator\JsonPointer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
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
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JsonPointerSerializer implements NormalizerInterface, CacheableSupportsMethodInterface
{

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }


    /**
     * @param JsonPointer $object
     */
    public function normalize($object, $format = null, array $context = []): string
    {
        if ($this->supportsNormalization($object, $format) === false) {
            throw new InvalidArgumentException();
        }

        return $object->getPointer();
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof JsonPointer;
    }
}
