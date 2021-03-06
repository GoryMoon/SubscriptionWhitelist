<?php

namespace App\Exceptions;

use App\Services\Csp\AppPolicy;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
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

    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     *
     * @return Response
     */
    public function render($request, Throwable $exception)
    {
        $this->container->singleton(AppPolicy::class, function () {
            return new AppPolicy();
        });
        app(AppPolicy::class)->addDirective(Directive::SCRIPT, Keyword::UNSAFE_INLINE);
        app(AppPolicy::class)->addDirective(Directive::STYLE, Keyword::UNSAFE_INLINE);

        return parent::render($request, $exception);
    }
}
