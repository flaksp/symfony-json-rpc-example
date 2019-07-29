<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\Sum;

class SumHandler
{
    public function __invoke(
        Sum $sum
    ): int {
        return $sum->getA() + $sum->getB();
    }
}
