<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'http://localhost/*', // Добавьте localhost сюда
        'http://127.0.0.1/*', // Если вы используете 127.0.0.1
        'broadcasting/auth',
    ];

    //add our custom getTokenFromRequest function
    protected function getTokenFromRequest($request)
    {
        //keep default behavior, use parent method first
        $token = parent::getTokenFromRequest($request);
        Log::info('CSRF Token from request: ' . $token);
        if (!$token) {
            $token = $request->cookie('XSRF-TOKEN');
        }
        return $token;
    }
}
