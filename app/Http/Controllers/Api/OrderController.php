<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'total_price' => 'required|numeric',
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'products' => $request->products,
            'total_price' => $request->total_price,
        ]);

        return response()->json($order, 201);
    }

    public function updateProducts(Request $request, $userId)
    {
        // Проверяем, что поле products является массивом и все ID товаров существуют в таблице chemicals
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:chemicals,id', // Проверка на существование ID товаров
        ]);

        $user = User::findOrFail($userId);

        // Обновляем товары пользователя с учетом связи через таблицу chemical_user
        $user->chemicals()->sync($request->products);

        return response()->json($user->chemicals, 200); // Возвращаем обновленный список товаров
    }
}
