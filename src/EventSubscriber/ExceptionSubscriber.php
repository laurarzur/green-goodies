<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    // Cette méthode permet d'intercepter les erreurs pour les gérér
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Si c'est une erreur HTTP, le code d'erreur et le message sont récupéré
        if ($exception instanceof HttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];

            $event->setResponse(new JsonResponse($data));
            // Si ce n'est pas le cas, le code d'erreur 500 est utilisé par défaut (ce qui ne devrait pas arriver)
        } else {
            $data = [
                'status' => 500,
                'message' => $exception->getMessage()
            ];

            $event->setResponse(new JsonResponse($data));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
