<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Message\Sum;
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

class SumSerializer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    public function denormalize($data, $class, $format = null, array $context = []): Sum
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        $violations = [];

        if (array_key_exists('a', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                $context + ['a']
            );
        }

        if (array_key_exists('b', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                'b'
            );
        }

        if (count($violations) > 0) {
            throw new DeserializationFailure($violations);
        }

        return new Sum(
            $data['a'],
            $data['b']
        );
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Sum::class && $format === JsonEncoder::FORMAT;
    }
}
