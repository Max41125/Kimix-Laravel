<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\ContractOrder;

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
            'products.*.product_id' => 'required|exists:chemical_user,id',
            'products.*.supplier_id' => 'required|exists:users,id', 
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
            'buyer_fullname' => 'nullable|string',
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
                'buyer_fullname' => $request->buyer_fullname,
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
                'product_id' => $product['product_id'],
                'supplier_id' => $product['supplier_id'],
            ]);
        }
    
        return response()->json(['order' => $order, 'address' => $address], 201);
    }
    

    public function updateProducts(Request $request, $userId)
    {
        // Проверяем, что поле products является массивом, что ID товаров существуют, и что unit_type, price, currency и description имеют допустимые значения
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'exists:chemicals,id', // Проверка на существование ID товаров
            'products.*.unit_type' => 'required|string|in:grams,kilograms,tons,pieces', // Проверка на типы единиц
            'products.*.price' => 'required|numeric|min:0', // Проверка на цену
            'products.*.currency' => 'required|string|in:RUB,USD,EUR,CNY', // Проверка валюты
            'products.*.description' => 'nullable|string|max:1000', // Описание продукта (необязательно, максимум 1000 символов)
        ]);
    
        $user = User::findOrFail($userId);
    
        // Обрабатываем каждый продукт и добавляем его вместе с типом единицы, ценой, валютой и описанием
        foreach ($request->products as $product) {
            $user->chemicals()->attach($product['id'], [
                'unit_type' => $product['unit_type'],
                'price' => $product['price'], // Добавляем цену
                'currency' => $product['currency'], // Добавляем валюту
                'description' => $product['description'] ?? null, // Добавляем описание (если есть)
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
        $products = $user->chemicals()->withPivot(['unit_type', 'price', 'currency',])->get();
    
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
                ->withPivot('unit_type', 'quantity', 'price', 'currency', 'supplier_id', 'product_id');
        }])
        ->get();

        return response()->json($orders, 200);
    }
    
    public function getUserOrder($orderId)
    {
        $order = Order::with([
                'products' => function ($query) use ($orderId) {
                    $query->where('chemical_order.order_id', $orderId)
                        ->withPivot('unit_type', 'quantity', 'price', 'currency', 'supplier_id', 'product_id');
                },
                'user' => function ($query) {
                    $query->with('userAddresses'); // Подгружаем связанные адреса пользователя
                }
            ])
            ->find($orderId); // Используем find для получения конкретного заказа
    
        // Проверяем, существует ли заказ
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        // Вернуть данные о заказе, продуктах, пользователе и его адресах
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
    
    public function createContractOrder(Request $request)
    {
        // Валидация входных данных
        $request->validate([
            'language' => 'required|string|in:ru,en',
            'order_id' => 'required|exists:orders,id',
            'user_id' => 'required|exists:users,id', // Убедиться, что пользователь существует
        ]);
    
        // Создаем или обновляем запись в таблице contract_order
        $contractOrder = ContractOrder::updateOrCreate(
            [
                'order_id' => $request->order_id,
                'user_id' => $request->user_id,
            ],
            [
                'language' => $request->language,
                'updated_at' => now(),
            ]
        );
    
        // Возвращаем успешный ответ
        return response()->json([
            'message' => 'Contract order created or updated successfully',
            'contract_orders' => $contractOrder,
        ], 200);
    }


    public function getContractOrder(Request $request, $orderId)
    {
        // Проверяем, что переданный order_id существует
        $contractOrder = ContractOrder::where('order_id', $orderId)->first();
    
        if (!$contractOrder) {
            return response()->json(['error' => 'Contract order not found'], 404);
        }
    
        return response()->json([
            'message' => 'Contract order retrieved successfully',
            'contract_order' => $contractOrder,
        ], 200);
    }
    
    

}
