<?php


namespace App\JsonRpc;


use App\JsonRpc\Response\AbstractResponse;
use Symfony\Component\Messenger\MessageBusInterface;

class ProcedureCallHandler
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function handle(
        ProcedureCall $procedureCall
    ): AbstractResponse {
        switch ($procedureCall->getMethod()) {
            case 'sum':
                $this->messageBus->dispatch()
        }


    }

    /**
     * @param ProcedureCall[] $procedureCalls
     * @return AbstractResponse[]
     */
    public function handleBatch(
        array $procedureCalls
    ): array {
        $responses = array_map(function (ProcedureCall $procedureCall): AbstractResponse {
            return $this->handle($procedureCall);
        }, $procedureCalls);

        return $responses;
    }
}
