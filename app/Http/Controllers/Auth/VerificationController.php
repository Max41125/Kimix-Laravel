<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{

    public function __invoke(EmailVerificationRequest $request)
    {
        // Верификация email
        $request->fulfill();

        
        return redirect()->to(env('FRONTEND_URL') . '/verification-success');
    }
}
