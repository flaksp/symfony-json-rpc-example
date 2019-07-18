<?php


namespace App\Validator\ConstraintViolation;



use App\Validator\ConstraintViolationParameter;
use App\Validator\JsonPointer;

class MandatoryFieldMissing implements ConstraintViolationInterface
{
    public const TYPE = 'mandatory_field_missing';

    /**
     * @var string[]
     */
    private $propertyPath;

    public function __construct(
        array $propertyPath
    ) {
        $this->propertyPath = $propertyPath;
    }

    public static function getType(): string
    {
        return self::TYPE;
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

    public function getDescription(): string
    {
        return sprintf(
            'Field "%s" is mandatory, but it\'s missing. Even if field is nullable it should be presented in request payload.',
            $this->getPointer()
        );
    }
}
