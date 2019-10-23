<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class JsonRpcContentTypeRemover
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if ($response->getContent() === '') {
            $response->headers->remove('Content-Type');
        }
    }
}
