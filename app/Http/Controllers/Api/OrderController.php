<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.id' => 'exists:chemicals,id', // Each product must exist
            'products.*.unit_type' => 'required|string|in:grams,kilograms,tons,pieces',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.quantity' => 'required|numeric|min:0',
            'products.*.currency' => 'required|string|in:RUB,USD,EUR,CNY',
            'products.*.supplier_id' => 'required|exists:users,id', // Добавлено поле supplier_id
            'total_price' => 'required|numeric',
            'currency' => 'required|string|in:RUB,USD,EUR,CNY',
            // User address fields
            'city' => 'required|string',
            'street' => 'required|string',
            'house' => 'required|string',
            'building' => 'nullable|string',
            'office' => 'nullable|string',
            'phone' => 'required|string',
            'inn' => 'nullable|string',
        ]);
    
        // Create or update the user's address
        $address = UserAddress::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'city' => $request->city,
                'street' => $request->street,
                'house' => $request->house,
                'building' => $request->building,
                'office' => $request->office,
                'phone' => $request->phone,
                'inn' => $request->inn,
            ]
        );
    
        // Create the order
        $order = Order::create([
            'user_id' => $request->user_id,
            'total_price' => $request->total_price,
            'currency' => $request->currency,
            'status' => 'new',
        ]);
    
        // Attach products to the order with their details, including supplier_id
        foreach ($request->products as $product) {
            $order->products()->attach($product['id'], [
                'unit_type' => $product['unit_type'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'currency' => $product['currency'],
                'supplier_id' => $product['supplier_id'], // Добавлено поле supplier_id
            ]);
        }
    
        return response()->json(['order' => $order, 'address' => $address], 201);
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
        $products = $user->chemicals()->withPivot(['unit_type', 'price', 'currency'])->get();
    
        // Вернуть список продуктов
        return response()->json($products, 200);
    }


    public function getUserOrders($userId)
    {
        // Найти пользователя
        $user = User::findOrFail($userId);
        
        // Получить заказы пользователя с продуктами
        $orders = $user->orders()->with('products')->get(); 
        
        // Вернуть список заказов с продуктами
        return response()->json($orders, 200);
    }
    
    public function getSellerOrders($sellerId)
    {
        // Получаем все заказы, где продавец указан как supplier_id в таблице chemical_order
        $orders = Order::whereHas('products', function ($query) use ($sellerId) {
            $query->where('chemical_order.supplier_id', $sellerId);
        })
        ->with(['products' => function ($query) use ($sellerId) {
            // Загружаем все продукты для этих заказов и фильтруем по sellerId
            $query->where('chemical_order.supplier_id', $sellerId)
                ->withPivot('unit_type', 'price', 'currency', 'supplier_id');
        }])
        ->get();

        return response()->json($orders, 200);
    }
    
    public function getUserOrder($orderId)
    {
        // Найти пользователя
        $order = Order::whereHas('products', function ($query) use ($orderId) {
            $query->where('chemical_order.order_id', $orderId);
        })
        ->with(['products' => function ($query) use ($orderId) {
            // Загружаем все продукты для этих заказов и фильтруем по sellerId
            $query->where('chemical_order.order_id', $orderId)
                ->withPivot('unit_type', 'price', 'currency', 'supplier_id');
        }])
        ->get();
        
        // Вернуть список заказов с продуктами
        return response()->json($order, 200);
    }


    
    public function getOrderStatus($orderId)
    {
        $order = Order::findOrFail($orderId);
        return response()->json(['status' => $order->status, 'current_status' => Order::$statuses[$order->status]], 200);
    }
    
    public function updateOrderStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', array_keys(Order::$statuses)),
        ]);
    
        $order = Order::findOrFail($orderId);
        $order->status = $request->status;
        $order->save();
    
        return response()->json(['message' => 'Статус обновлен', 'order' => $order], 200);
    }
    


}
