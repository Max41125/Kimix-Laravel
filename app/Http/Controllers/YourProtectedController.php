<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YourProtectedController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'user' => Auth::user(),
        ]);
    }
}
