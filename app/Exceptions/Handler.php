<?php

namespace App\Exceptions;

use App\Support\Panic;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use LaravelZero\Framework\Exceptions\ConsoleException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        ConsoleException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if (app()->isProduction()) {
                Panic::abort($e);
            }

            return true;
        });
    }
}
