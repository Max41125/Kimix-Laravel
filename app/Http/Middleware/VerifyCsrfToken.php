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
        //
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
