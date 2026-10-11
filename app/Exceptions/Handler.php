<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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

        $this->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            if (str_contains($e->getMessage(), 'refused') || str_contains($e->getMessage(), 'SQLSTATE[HY000] [2002]')) {
                return back()->withErrors([
                    'email' => 'MySQL Server Offline: Aapke computer par MySQL (port 3306) chal nahi raha. Mehar Sweets database connect karne ke liye XAMPP/MySQL start karein.',
                ]);
            }
        });
    }
}
