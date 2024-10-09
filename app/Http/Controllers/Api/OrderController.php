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
    
        // Добавляем новые товары для пользователя, не удаляя существующие
        $user->chemicals()->attach($request->products);
    
        // Возвращаем обновленный список товаров
        return response()->json($user->chemicals, 200);
    }
    
    public function removeProducts(Request $request, $userId)
    {
        // Проверяем, что поле products является массивом и все ID товаров существуют в таблице chemicals
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:chemicals,id', // Проверка на существование ID товаров
        ]);
    
        $user = User::findOrFail($userId);
    
        // Удаляем связи между пользователем и товарами
        $user->chemicals()->detach($request->products);
    
        // Возвращаем обновленный список товаров
        return response()->json($user->chemicals, 200);
    }
    




}
