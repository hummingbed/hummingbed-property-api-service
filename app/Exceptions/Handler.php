<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if (! $request->is('api/*')) {
            return parent::render($request, $e);
        }

        return $this->apiExceptionResponse($e);
    }

    private function apiExceptionResponse(Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'status' => 'failed',
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], $e->status);
        }

        $status = match (true) {
            $e instanceof AuthenticationException => Response::HTTP_UNAUTHORIZED,
            $e instanceof AuthorizationException => Response::HTTP_FORBIDDEN,
            $e instanceof EntityNotFoundException, $e instanceof ModelNotFoundException => Response::HTTP_NOT_FOUND,
            $e instanceof HttpExceptionInterface => $e->getStatusCode(),
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };

        $message = match (true) {
            $e instanceof AuthenticationException => 'Unauthenticated.',
            $e instanceof AuthorizationException => 'This action is unauthorized.',
            $e instanceof ModelNotFoundException => 'Resource not found.',
            default => $e->getMessage() ?: Response::$statusTexts[$status] ?? 'Request failed',
        };
        if ($status === Response::HTTP_INTERNAL_SERVER_ERROR && ! config('app.debug')) {
            $message = 'An unexpected error occurred.';
        }

        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'data' => null,
        ], $status);
    }
}
