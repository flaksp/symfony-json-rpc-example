<?php


namespace App\Validator\ConstraintViolation;


use App\Validator\ConstraintViolationParameter;
use App\Validator\JsonPointer;

interface ConstraintViolationInterface
{
    public static function getType(): string;

    /**
     * @return ConstraintViolationParameter[]
     */
    public function getParameters(): array;

    public function getPointer(): JsonPointer;

    public function getDescription(): string;
}
