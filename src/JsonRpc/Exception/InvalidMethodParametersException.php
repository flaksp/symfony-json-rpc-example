<?php

declare(strict_types=1);

namespace App\JsonRpc\Exception;

use App\Validator\ConstraintViolation\ConstraintViolationInterface;

class InvalidMethodParametersException extends AbstractJsonRpcException
{
    /**
     * @var ConstraintViolationInterface[]
     */
    private $constraintViolations;

    /**
     * @param ConstraintViolationInterface[] $constraintViolations
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
