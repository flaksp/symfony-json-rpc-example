<?php

declare(strict_types=1);

namespace App\Serializer;

use App\JsonRpc\ProcedureCall;
use App\JsonRpc\ProcedureCallHandler;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ProcedureCallSerializer implements DenormalizerInterface, DenormalizerAwareInterface, rCacheableSupportsMethodInterface
{
    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === get_class($this);
    }

    /**
     * @param Barcode    $object
     * @param mixed|null $format
     *
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($object, $format)) {
            throw new InvalidArgumentException('Could not normalize the object');
        }

        return $object->getCode();
    }


    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed $data Data to restore
     * @param string $class The expected class to instantiate
     * @param string $format Format the given data was extracted from
     * @param array $context Options available to the denormalizer
     *
     * @return object
     *
     * @throws BadMethodCallException   Occurs when the normalizer is not called in an expected context
     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
     * @throws LogicException           Occurs when the normalizer is not supposed to denormalize
     * @throws RuntimeException         Occurs if the class cannot be instantiated
     * @throws ExceptionInterface       Occurs for all the other cases of errors
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if ($this->supportsDenormalization($data, $class, $format) === false) {
            throw new LogicException();
        }

        if ()
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === ProcedureCallHandler::class && $format === JsonEncoder::FORMAT;
    }
}
