<?php

namespace App\Exceptions;

use App\Traits\HandleErrors;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    // public function render($request, Throwable $exception)
    // {
    //     if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $exception->getStatusCode() === 403) {
    //         return response()->view('errors.403', [], Response::HTTP_FORBIDDEN);
    //     }

    //     return parent::render($request, $exception);
    // }

    use HandleErrors;

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return $this->handleException($e);
        }

        return parent::render($request, $e);
    }
}
