<?php

declare(strict_types=1);

namespace App\Core\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse();
        $response->setContent($this->exceptionToJson($exception));

        // HttpException содержит информацию о заголовках и статусе, испольузем это
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        }
        else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }

    public function exceptionToJson(\Throwable $exception): string
    {
        $json = ['error' => 'Internal server error'];

        //if ($this->env === 'dev') {
        $json = [
            'error' => $exception->getMessage(),
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ];
        //}

        return json_encode($json);
    }
}