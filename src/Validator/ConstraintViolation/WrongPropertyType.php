<?php


namespace App\Validator\ConstraintViolation;



use App\Validator\ConstraintViolationParameter;
use App\Validator\JsonPointer;

/**
 * This error appears when property does not accept
 * given type.
 */
class WrongPropertyType implements ConstraintViolationInterface
{
    public const TYPE = 'wrong_property_type';

    /**
     * @var string[]
     */
    private $propertyPath;

    /**
     * @var string
     */
    private $givenType;

    /**
     * @var string[]
     */
    private $allowedTypes;

    /**
     * @var ConstraintViolationParameter[]
     */
    private $parameters;

    /**
     * @param string[] $propertyPath
     * @param string[] $allowedTypes
     */
    public function __construct(
        array $propertyPath,
        string $givenType,
        array $allowedTypes
    ) {
        $this->propertyPath = $propertyPath;
        $this->givenType = $givenType;
        $this->allowedTypes = $allowedTypes;
        $this->parameters = [
            new ConstraintViolationParameter(
                'givenType',
                $this->givenType
            ),
            new ConstraintViolationParameter(
                'allowedTypes',
                $this->allowedTypes
            )
        ];
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
        return $this->parameters;
    }

    public function getPointer(): JsonPointer
    {
        return new JsonPointer($this->propertyPath);
    }

    public function getDescription(): string
    {
        return sprintf(
            'Property "%s" is %s type, but only types %s are allowed.',
            $this->getPointer(),
            $this->givenType,
            implode(', ', $this->allowedTypes)
        );
    }
}
