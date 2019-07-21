<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Validator\ConstraintViolationParameter;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ConstraintViolationParameterSerializer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    /**
     * @param ConstraintViolationParameter $object
     * @param mixed|null                   $format
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if ($this->supportsNormalization($object, $format) === false) {
            throw new InvalidArgumentException();
        }

        return [
            'name' => $object->getName(),
            'value' => $object->getValue(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ConstraintViolationParameter && $format === JsonEncoder::FORMAT;
    }
}
