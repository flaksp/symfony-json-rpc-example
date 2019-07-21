<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Message\Sum;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolation\ConstraintViolationInterface;
use App\Validator\ConstraintViolation\MandatoryFieldMissing;
use App\Validator\ConstraintViolation\WrongPropertyType;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SumSerializer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    public function denormalize($data, $class, $format = null, array $context = []): Sum
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        /** @var ConstraintViolationInterface[] $violations */
        $violations = [];

        if (array_key_exists('a', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                $context['propertyPath'] + ['a']
            );
        } else {
            if (is_int($data) === false) {
                $violations[] = new WrongPropertyType(
                    $context['propertyPath'] + ['a'],
                    gettype($data),
                    ['integer']
                );
            }
        }

        if (array_key_exists('b', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                $context['propertyPath'] + ['b']
            );
        } else {
            if (is_int($data) === false) {
                $violations[] = new WrongPropertyType(
                    $context['propertyPath'] + ['b'],
                    gettype($data),
                    ['integer']
                );
            }
        }

        if (count($violations) > 0) {
            throw new DeserializationFailure($violations);
        }

        return new Sum($data['a'], $data['b']);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Sum::class;
    }
}
