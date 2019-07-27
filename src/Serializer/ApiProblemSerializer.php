<?php

declare(strict_types=1);

namespace App\Serializer;

use App\ValueObject\ApiProblem\ApiProblem;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiProblemSerializer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    /**
     * @param ApiProblem $object
     * @param mixed|null $format
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if ($this->supportsNormalization($object, $format) === false) {
            throw new InvalidArgumentException();
        }

        return [
            'type' => $object->getType(),
            'detail' => $object->getDetail(),
            'status' => $object->getStatus(),
            'title' => $object->getTitle(),
            'instance' => $object->getInstance(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ApiProblem && $format === JsonEncoder::FORMAT;
    }
}
