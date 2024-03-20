<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use PDOException;
use Illuminate\Support\Facades\Log;
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

    public function render ($request, Throwable $e)
    {
        if ($e instanceof \PDOException || $e instanceof QueryException) {
            Log::channel('slackNotification')
                ->error('Database Exception: '.$e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);

            // return redirect()->back()->withErrors('error', 'Ha ocurrido un error, vuelva a intentarlo más tarde');
            return response()->view('error', ['message' => 'Ha ocurrido un error, vuelva a intentarlo más tarde']);
        }

        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->back()->withErrors('error', 'Error al enviar el formulario. Por favor, intente de nuevo');
        }

        return parent::render($request, $e);
    }

}
