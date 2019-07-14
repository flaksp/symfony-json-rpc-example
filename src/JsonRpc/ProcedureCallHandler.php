<?php


namespace App\JsonRpc;


use App\JsonRpc\Response\AbstractResponse;
use App\JsonRpc\Response\BatchResponse;

class ProcedureCallHandler
{
    public function handle(
        ProcedureCall $procedureCall
    ): AbstractResponse
    {

    }

    public function handleBatch(
        BatchProcedureCall $batchProcedureCall
    ): BatchResponse
    {
        $responses = array_map(function (ProcedureCall $procedureCall): AbstractResponse {
            return $this->handle($procedureCall);
        }, $batchProcedureCall->getProcedureCalls());

        return new BatchResponse($responses);
    }
}
