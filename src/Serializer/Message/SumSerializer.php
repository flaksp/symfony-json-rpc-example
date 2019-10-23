<?php

declare(strict_types=1);

namespace App\Serializer\Message;

use App\Message\Sum;
use App\Serializer\Exception\DeserializationFailure;
use App\Validator\ConstraintViolation\MandatoryFieldMissing;
use App\Validator\ConstraintViolation\WrongPropertyType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SumSerializer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = []): Sum
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        $violations = [];

        if (array_key_exists('a', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                array_merge($context['propertyPath'], ['a'])
            );
        } else {
            if (is_int($data['a']) === false) {
                $violations[] = new WrongPropertyType(
                    array_merge($context['propertyPath'], ['a']),
                    gettype($data['a']),
                    ['integer', 'float']
                );
            }
        }

        if (array_key_exists('b', $data) === false) {
            $violations[] = new MandatoryFieldMissing(
                array_merge($context['propertyPath'], ['b'])
            );
        } else {
            if (is_int($data['b']) === false) {
                $violations[] = new WrongPropertyType(
                    array_merge($context['propertyPath'], ['b']),
                    gettype($data['b']),
                    ['integer', 'float']
                );
            }
        }

        if (count($violations) > 0) {
            throw new DeserializationFailure($violations);
        }

        return new Sum(
            $data['a'],
            $data['b']
        );
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Sum::class && $format === JsonEncoder::FORMAT;
    }
}
