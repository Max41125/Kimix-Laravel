<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    // Получение данных продавца по ID пользователя
    public function show($sellerId)
    {
        $seller = Seller::where('user_id', $sellerId)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found'], 200);
        }

        return response()->json($seller);
    }

    public function updateSellerInfo(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id', // Проверяем, что user_id существует
            'type' => 'nullable|string|in:ИП,ООО',
            'full_name' => 'nullable|string',
            'short_name' => 'nullable|string',
            'legal_address' => 'nullable|string',
            'actual_address' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'inn' => 'nullable|string|unique:sellers,inn,' . ($request->seller_id ?? 'NULL'),
            'kpp' => 'nullable|string',
            'ogrn' => 'nullable|string',
            'director' => 'nullable|string',
            'chief_accountant' => 'nullable|string',
            'authorized_person' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bik' => 'nullable|string',
            'corr_account' => 'nullable|string',
            'settlement_account' => 'nullable|string',
            'okved' => 'nullable|string',
            'tax_system' => 'nullable|string',
        ]);

        // Ищем или создаем запись продавца
        $seller = Seller::updateOrCreate(
            ['user_id' => $validated['user_id']], // Поиск по user_id
            $validated // Обновление данных
        );

        return response()->json($seller, 200);
    }
}
