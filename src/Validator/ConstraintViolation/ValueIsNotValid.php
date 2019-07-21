<?php


namespace App\Validator\ConstraintViolation;



use App\Validator\ConstraintViolationParameter;
use App\Validator\JsonPointer;

/**
 * This error appears when others constraint violations
 * are not relevant. Think about this like about violation
 * with custom error message.
 */
class ValueIsNotValid implements ConstraintViolationInterface
{
    public const TYPE = 'value_is_not_valid';

    /**
     * @var string[]
     */
    private $propertyPath;

    /**
     * @var string
     */
    private $message;

    /**
     * @param string[] $propertyPath
     */
    public function __construct(
        array $propertyPath,
        string $message
    ) {
        $this->propertyPath = $propertyPath;
        $this->message = $message;
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
            'Property "%s" is not valid. %s',
            $this->getPointer(),
            $this->message
        );
    }
}
