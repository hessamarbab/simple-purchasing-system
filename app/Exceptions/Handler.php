<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $this->renderable(function (CustomizedException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'message' => "Not Found"
            ], 404);
        });
    }
}
