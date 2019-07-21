<?php

declare(strict_types=1);

namespace App\Validator\ConstraintViolation;

use App\Validator\ConstraintViolationParameter;
use App\Validator\JsonPointer;

/**
 * This error appears when field should be present in
 * the object but it is missing.
 */
class MandatoryFieldMissing implements ConstraintViolationInterface
{
    public const TYPE = 'mandatory_field_missing';

    /**
     * @var string[]
     */
    private $propertyPath;

    /**
     * @param string[] $propertyPath
     */
    public function __construct(
        array $propertyPath
    ) {
        $this->propertyPath = $propertyPath;
    }

    public static function getType(): string
    {
        return self::TYPE;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Property "%s" is mandatory, but it\'s missing. Even if field is nullable it should be presented in request payload.',
            $this->getPointer()
        );
    }

    /**
     * @return ConstraintViolationParameter[]
     */
    public function getParameters(): array
    {
        return [];
    }

    public function getPointer(): JsonPointer
    {
        return new JsonPointer($this->propertyPath);
    }
}
