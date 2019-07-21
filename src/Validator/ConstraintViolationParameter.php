<?php


namespace App\Validator;


class ConstraintViolationParameter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int|string
     */
    private $value;

    /**
     * @param string|int|(string|int)[] $value
     */
    public function __construct(
        string $name,
        $value
    ) {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }
}
