<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Validator\ConstraintViolation\ConstraintViolationInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ConstraintViolationSerializer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    /**
     * @param ConstraintViolationInterface $object
     * @param mixed|null                   $format
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if ($this->supportsNormalization($object, $format) === false) {
            throw new InvalidArgumentException();
        }

        return [
            'type' => $object::getType(),
            'description' => $object->getDescription(),
            'parameters' => $this->normalizer->normalize(
                $object->getParameters(),
                $format,
                $context
            ),
            'pointer' => $this->normalizer->normalize(
                $object->getPointer(),
                $format,
                $context
            ),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ConstraintViolationInterface && $format === JsonEncoder::FORMAT;
    }
}
