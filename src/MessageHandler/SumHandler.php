<?php


namespace App\MessageHandler;


use App\JsonRpc\ProcedureCall;
use App\JsonRpc\Response\AbstractResponse;

class SumHandler
{
    public function __invoke(
        ProcedureCall $procedureCall
    ): AbstractResponse {
        if ($procedureCall->getMethod() !== 'sum') {
            return;
        }

        $parameters = $procedureCall->getParameters();

        $a = $parameters['a'];
        $b = $parameters['b'];

        return $a + $b;
    }
}
