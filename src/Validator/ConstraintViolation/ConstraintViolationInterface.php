<?php

declare(strict_types=1);

namespace App\Validator\ConstraintViolation;

use App\Validator\ConstraintViolationParameter;
use App\Validator\JsonPointer;

interface ConstraintViolationInterface
{
    public static function getType(): string;

    public function getDescription(): string;

    /**
     * @return ConstraintViolationParameter[]
     */
    public function getParameters(): array;

    public function getPointer(): JsonPointer;
}
