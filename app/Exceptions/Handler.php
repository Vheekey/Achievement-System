<?php

namespace App\Exceptions;

use App\Traits\HasJsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HasJsonResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * {@inheritDoc}
     */
    public function render($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        if ($response = $this->responsableException($exception, $request)) {
            return $response;
        }

        if ($exception instanceof HttpException) {
            return $this->convertHttpExceptionToJson($exception);
        }

        if (! app()->environment('local')) {
            return $this->jsonResponse(HTTP_SERVER_ERROR, 'server_error', $this->convertExceptionToArray($exception));
        }

        return parent::render($request, $exception);
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareException(Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return new NotFoundHttpException('Resource not found');
        } elseif ($e instanceof InvalidSignatureException) {
            return new HttpException(HTTP_FORBIDDEN, 'This url is no longer valid or has expired!');
        } elseif ($e instanceof AuthenticationException) {
            return new HttpException(HTTP_UNAUTHORIZED, $e->getMessage(), $e);
        } elseif ($e instanceof UnauthorizedException) {
            return new HttpException(HTTP_FORBIDDEN, $e->getMessage(), $e);
        }

        return parent::prepareException($e);
    }

    protected function responsableException(Throwable $exception, $request): ?JsonResponse
    {
        if ($exception instanceof HttpResponseException) {
            return $this->wrapJsonResponse($exception->getResponse(), 'An error occurred.');
        }

        if ($exception instanceof ValidationException) {
            return (new ValidationException($exception->validator))->render($request);
        }

        return null;
    }

    protected function convertHttpExceptionToJson(HttpException $exception): JsonResponse
    {
        $statusCode = $exception->getStatusCode();
        $message = $exception->getMessage() ?: Response::$statusTexts[$statusCode];
        $headers = $exception->getHeaders();
        $data = null;

        return $this->jsonResponse($statusCode, $message, $data, $headers);
    }
}
