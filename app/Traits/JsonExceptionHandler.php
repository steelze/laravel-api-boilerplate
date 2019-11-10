<?php

namespace App\Traits;

use Exception;
use App\Helper\ResponseHelper;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

/**
 *  Handles exception for requests expecting JSON
 */
trait JsonExceptionHandler
{
    public function exception($request, Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return ResponseHelper::withError('Route Not Found', [], Response::HTTP_NOT_FOUND, $exception->getHeaders());
        }

        if ($exception instanceof ModelNotFoundException) {
            return ResponseHelper::withError(
                'Entry for '.str_replace('App\\', '', $exception->getModel()).' not found',
                [],
                Response::HTTP_NOT_FOUND
            );
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return ResponseHelper::withError($exception->getMessage(), [], Response::HTTP_METHOD_NOT_ALLOWED, $exception->getHeaders());
        }

        if ($exception instanceof UnauthorizedHttpException) {
            $previous_exception = $exception->getPrevious();
            if ($previous_exception instanceof TokenExpiredException) {
                return ResponseHelper::withError('Session has expired. Signin again to continue', [], Response::HTTP_UNAUTHORIZED, $exception->getHeaders());
            } elseif ($previous_exception instanceof TokenInvalidException) {
                return ResponseHelper::withError('Session is invalid. Signin again to continue', [], Response::HTTP_UNAUTHORIZED, $exception->getHeaders());
            } elseif ($previous_exception instanceof TokenBlacklistedException) {
                return ResponseHelper::withError('This session has been blacklisted. Signin again to continue', [], Response::HTTP_UNAUTHORIZED, $exception->getHeaders());
            } else {
                return ResponseHelper::withError('Session is invalid. Signin again to continue', [], Response::HTTP_UNAUTHORIZED, $exception->getHeaders());
            }
        }

        if ($exception instanceof AuthorizationException) {
            return ResponseHelper::withError(
                $exception->getMessage(),
                [],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($exception instanceof ThrottleRequestsException) {
            return ResponseHelper::withError(
                $exception->getMessage(),
                [],
                Response::HTTP_TOO_MANY_REQUESTS,
                $exception->getHeaders(),
            );
        }

        if ($exception instanceof FatalThrowableError) {
            return ResponseHelper::withError(
                'Server Error',
                (app()->environment('production') || config('app.debug') == false) ?
                [] :
                [
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ((app()->environment('production') || config('app.debug') == false)) {
            return ResponseHelper::withError(
                'Server Error',
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } else {
            return parent::render($request, $exception);
        }
    }
}
