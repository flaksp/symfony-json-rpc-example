<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\Response\AbstractResponse;
use App\JsonRpc\Response\ErrorResponse;
use App\JsonRpc\Response\SuccessfulResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JsonRpcResponseSerializer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    /**
     * @param AbstractResponse $object
     * @param mixed|null       $format
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        if ($this->supportsNormalization($object, $format) === false) {
            throw new InvalidArgumentException();
        }

        $schema = [
            'id' => $this->normalizer->normalize(
                $object->getId(),
                $format,
                $context
            ),
            'jsonrpc' => $this->normalizer->normalize(
                $object->getVersion(),
                $format,
                $context
            ),
        ];

        if ($object instanceof SuccessfulResponse) {
            $schema['result'] = $this->normalizer->normalize(
                $object->getResult(),
                $format,
                $context
            );
        } elseif ($object instanceof ErrorResponse) {
            $schema['error'] = $this->normalizer->normalize(
                $object->getError(),
                $format,
                $context
            );
        } else {
            throw new InvalidArgumentException();
        }

        return $schema;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AbstractResponse && $format === JsonEncoder::FORMAT;
    }
}
