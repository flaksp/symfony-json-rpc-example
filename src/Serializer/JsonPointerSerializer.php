<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Validator\JsonPointer;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JsonPointerSerializer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    /**
     * @param JsonPointer $object
     * @param mixed|null  $format
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
