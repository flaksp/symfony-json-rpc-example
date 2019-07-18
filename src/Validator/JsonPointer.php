<?php


namespace App\Validator;


class JsonPointer
{
    /**
     * @var string[]
     */
    private $propertyPath;

    /**
     * @param (string|int)[] $propertyPath
     */
    public function __construct(
        array $propertyPath
    ) {
        $this->propertyPath = $propertyPath;
    }

    /**
     * @return (string|int)[] $propertyPath
     */
    public function getPropertyPath(): array
    {
        return $this->propertyPath;
    }

    public function getPointer(): string
    {
        $pointer = '#';

        foreach ($this->propertyPath as $pathItem) {
            $pointer .= '/' . $pathItem;
        }

        return $pointer;
    }
}
