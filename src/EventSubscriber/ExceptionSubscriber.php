<?php

namespace App\EventSubscriber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if($exception instanceof NotFoundHttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => 'Resource non trouvée !!'
            ];

            $response = new JsonResponse($data);
            $event->setResponse($response);
        }
        else{
            $data = [
                'Erreur' => $exception->getMessage()
            ];
            $response = new JsonResponse($data);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
    /*
        1 - php bin/console make:subscriber
        2 - repondre : EventSubscriber
        3 - ensuite : kernel.exception
    */