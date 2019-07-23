<?php

declare(strict_types=1);

namespace App\Serializer\Exception;

use App\Validator\ConstraintViolation\ConstraintViolationInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class DeserializationFailure extends UnexpectedValueException
{
    /**
     * @var ConstraintViolationInterface[]
     */
    private $constraintViolations;

    /**
     * @param ConstraintViolationInterface[]
     */
    public function __construct(
        array $constraintViolations
    ) {
        $this->constraintViolations = $constraintViolations;

        $violationIndex = 1;

        $message = array_reduce($constraintViolations, function (string $carry, ConstraintViolationInterface $violation) use (&$violationIndex): string {
            return $carry .= sprintf(
                '%d) %s' . "\n",
                $violationIndex++,
                $violation->getDescription()
            );
        }, "\n");

        parent::__construct($message);
    }

    /**
     * @return ConstraintViolationInterface[]
     */
    public function getConstraintViolations(): array
    {
        return $this->constraintViolations;
    }
}
