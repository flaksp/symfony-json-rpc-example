<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\Error;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JsonRpcErrorSerializer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    /**
     * @param Error      $object
     * @param mixed|null $format
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if ($this->supportsNormalization($object, $format) === false) {
            throw new InvalidArgumentException();
        }

        $schema = [
            'code' => $object->getCode(),
            'message' => $object->getMessage(),
        ];

        if ($object->getData() !== null) {
            $schema['data'] = $this->normalizer->normalize(
                $object->getData(),
                $format,
                $context
            );
        }

        return $schema;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Error && $format === JsonEncoder::FORMAT;
    }
}
