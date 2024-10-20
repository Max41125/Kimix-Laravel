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
            'unit_type' => 'required|string|in:grams,kilograms,tons,pieces',
            'currency' => 'required|string|in:RUB,USD,EUR,CNY', // Проверка валюты
        ]);

        $order = Order::create([
            'user_id' => $request->user_id,
            'products' => $request->products,
            'total_price' => $request->total_price,
            'unit_type' => $request->unit_type,
            'currency' => $request->currency, // Сохраняем валюту
        ]);

        return response()->json($order, 201);
    }

    public function updateProducts(Request $request, $userId)
    {
        // Проверяем, что поле products является массивом, что ID товаров существуют, и что unit_type, price и currency имеют допустимые значения
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'exists:chemicals,id', // Проверка на существование ID товаров
            'products.*.unit_type' => 'required|string|in:grams,kilograms,tons,pieces', // Проверка на типы единиц
            'products.*.price' => 'required|numeric|min:0', // Проверка на цену
            'products.*.currency' => 'required|string|in:RUB,USD,EUR,CNY', // Проверка валюты
        ]);

        $user = User::findOrFail($userId);

        // Обрабатываем каждый продукт и добавляем его вместе с типом единицы и ценой
        foreach ($request->products as $product) {
            $user->chemicals()->attach($product['id'], [
                'unit_type' => $product['unit_type'],
                'price' => $product['price'], // Добавляем цену
                'currency' => $product['currency'], // Добавляем валюту
            ]);
        }

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
    

    public function getUserProducts($userId)
    {
        // Найти пользователя
        $user = User::findOrFail($userId);
    
        // Получить список связанных продуктов
        $products = $user->chemicals;
    
        // Вернуть список продуктов
        return response()->json($products, 200);
    }
}
