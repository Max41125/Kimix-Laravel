<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    public function show()
    {
        $seller = Auth::user()->seller;
        return response()->json($seller);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'string|in:ИП,ООО', // Значение должно быть либо ИП, либо ООО
            'full_name' => 'string',
            'short_name' => 'nullable|string',
            'legal_address' => 'string',
            'actual_address' => 'string',
            'email' => 'email',
            'phone' => 'string',
            'inn' => 'string|unique:sellers,inn',
            'kpp' => 'nullable|string',
            'ogrn' => 'string',
            'director' => 'string',
            'chief_accountant' => 'nullable|string',
            'authorized_person' => 'nullable|string',
            'bank_name' => 'string',
            'bik' => 'string',
            'corr_account' => 'string',
            'settlement_account' => 'string',
            'okved' => 'nullable|string',
            'tax_system' => 'string',
        ]);
    
        $seller = Auth::user()->seller()->create($validated);
    
        return response()->json($seller, 201);
    }
    

    public function update(Request $request)
    {
        $seller = Auth::user()->seller;
    
        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 404);
        }
    
        $validated = $request->validate([
            'type' => 'string|in:ИП,ООО',
            'full_name' => 'string',
            'short_name' => 'nullable|string',
            'legal_address' => 'string',
            'actual_address' => 'string',
            'email' => 'email',
            'phone' => 'string',
            'inn' => 'string|unique:sellers,inn,' . $seller->id,
            'kpp' => 'nullable|string',
            'ogrn' => 'string',
            'director' => 'string',
            'chief_accountant' => 'nullable|string',
            'authorized_person' => 'nullable|string',
            'bank_name' => 'string',
            'bik' => 'string',
            'corr_account' => 'string',
            'settlement_account' => 'string',
            'okved' => 'nullable|string',
            'tax_system' => 'string',
        ]);
    
        $seller->update($validated);
    
        return response()->json($seller);
    }
    
}
