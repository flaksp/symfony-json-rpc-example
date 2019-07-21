<?php

declare(strict_types=1);

namespace App\Message;

class Sum
{
    private $a;

    private $b;

    public function __construct(
        $a,
        $b
    ) {
        $this->a = $a;
        $this->b = $b;
    }

    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }
}
