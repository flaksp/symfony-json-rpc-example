<?php


namespace App\JsonRpc;


class BatchProcedureCall
{
    /**
     * @var ProcedureCall[]
     */
    private $procedureCalls;

    /**
     * @param ProcedureCall[] $procedureCalls
     */
    public function __construct(
        array $procedureCalls
    ) {
        $this->procedureCalls = $procedureCalls;
    }

    /**
     * @return ProcedureCall[]
     */
    public function getProcedureCalls(): array
    {
        return $this->procedureCalls;
    }
}
