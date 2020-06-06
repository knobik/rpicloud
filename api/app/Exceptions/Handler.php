<?php

namespace App\Exceptions;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use InvalidArgumentException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * List of HTTP return codes for exceptions.
     *
     * @var array
     */
    static public array $responseCodes = [
        ValidationException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
        MethodNotAllowedHttpException::class => Response::HTTP_METHOD_NOT_ALLOWED,
        NotFoundHttpException::class => Response::HTTP_NOT_FOUND,
        AuthenticationException::class => Response::HTTP_UNAUTHORIZED,
        UnauthorizedException::class => Response::HTTP_FORBIDDEN,
        InvalidArgumentException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
    ];

    /**
     * Default exception messages
     *
     * @var array
     */
    static public array $responseMessages = [
        NotFoundHttpException::class => 'errors.exceptions.NotFoundHttpException',
        MethodNotAllowedHttpException::class => 'errors.exceptions.MethodNotAllowedHttpException',
        AuthenticationException::class => 'errors.exceptions.AuthenticationException',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $e
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return $this->generateResponse($e);
        }

        return parent::render($request, $e);
    }

    /**
     * @param  Throwable  $exception
     * @return string
     */
    protected function getExceptionType(Throwable $exception): string
    {
        $class = get_class($exception);
        return class_basename($class);
    }

    /**
     * @param  Throwable  $exception
     * @param  null  $default
     * @return null
     */
    private function getExceptionResponse(Throwable $exception, $default = null)
    {
        if ($exception instanceof ValidationException) {
            return $exception->errors();
        }

        if ($exception instanceof HttpResponseException) {
            return $exception->getResponse()->getContent();
        }

        if ($exception instanceof RequestException && $exception->getResponse() !== null) {
            return $exception->getResponse()->getBody()->getContents();
        }

        return $default;
    }

    /**
     * @param  Throwable  $exception
     * @param  int  $default
     * @return int|mixed
     */
    protected function getExceptionCode(Throwable $exception, $default = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $class = get_class($exception);
        return self::$responseCodes[$class] ?? $default;
    }

    /**
     * @param  Throwable  $exception
     * @return string
     */
    protected function getExceptionMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();

        $class = get_class($exception);
        if (array_key_exists($class, self::$responseMessages)) {
            $message = trans(self::$responseMessages[$class]);
        }

        return $message;
    }

    /**
     * @param  Throwable  $exception
     * @return JsonResponse
     */
    protected function generateResponse(Throwable $exception): JsonResponse
    {
        return response()->json(
            [
                'error' => $this->getExceptionType($exception),
                'message' => $this->getExceptionMessage($exception),
                'data' => $this->getExceptionResponse($exception),
            ],
            $this->getExceptionCode($exception)
        );
    }
}
