<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'chemical_id' => 'required|exists:chemicals,id',
            'type' => 'required|in:buyer,seller,student',
            'duration' => 'required|in:3 months,6 months,1 year,3 years',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $user = User::find($request->user_id);
    
        $subscriptionData = [
            'type' => $request->type,
            'duration' => $request->duration,
            'start_date' => now(),
            'end_date' => $this->parseDuration($request->duration),
        ];
    
        // Проверяем, есть ли уже подписка для данного user_id и chemical_id
        $subscription = Subscription::where('user_id', $request->user_id)
            ->where('chemical_id', $request->chemical_id)
            ->first();
    
        if ($subscription) {
            // Если подписка уже есть, просто обновляем данные
            $subscription->update($subscriptionData);
            return response()->json(['message' => 'Subscription updated', 'subscription' => $subscription], 200);
        } else {
            // Если подписки нет, создаем новую
            $subscriptionData['user_id'] = $request->user_id;
            $subscriptionData['chemical_id'] = $request->chemical_id;
            $subscription = Subscription::create($subscriptionData);
            return response()->json(['message' => 'Subscription created', 'subscription' => $subscription], 200);
        }
    }
    


    function parseDuration($duration) {
        switch ($duration) {
            case '3 months': return Carbon::now()->addMonths(3);
            case '6 months': return Carbon::now()->addMonths(6);
            case '1 year': return Carbon::now()->addYear();
            case '3 years': return Carbon::now()->addYears(3);
            default: return Carbon::now(); 
        }
    }


    public function updateSubscription(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
    
        $validator = Validator::make($request->all(), [
            'chemical_id' => 'sometimes|exists:chemicals,id', // Добавлено
            'type' => 'sometimes|in:buyer,seller,student',
            'duration' => 'sometimes|in:3 months,6 months,1 year,3 years',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        if ($request->has('duration')) {
            $subscription->end_date = $subscription->start_date->add($request->duration);
        }
    
        // Обновляем chemical_id, если он передан
        $subscription->update($request->only(['chemical_id', 'type', 'duration']));
    
        return response()->json($subscription);
    }

    public function cancelSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return response()->json(null, 204);
    }

    public function getUserSubscriptions($userId)
    {
        $user = User::findOrFail($userId);
        $subscriptions = $user->subscriptions;
    
        if ($subscriptions->isEmpty()) {
            return response()->json(null, 204);
        }
    
        return response()->json($subscriptions);
    }
    
    
    // Для интеграции эквайринга



    // public function initiatePayment(Request $request)
    // {
    //     $subscription = Subscription::findOrFail($request->subscription_id);
    
    //     // Данные для запроса к API TCB
    //     $paymentData = [
    //         'amount' => $this->calculateAmount($subscription->type, $subscription->duration),
    //         'currency' => 'UZS', // или другая валюта
    //         'description' => 'Оплата подписки ' . $subscription->type,
    //         'callback_url' => route('payment.callback'), // URL для callback от TCB
    //     ];
    
    //     // Отправка запроса к API TCB
    //     $response = Http::post('https://api.tcb.uz/payment', $paymentData);
    
    //     if ($response->successful()) {
    //         $subscription->payment_id = $response->json('payment_id');
    //         $subscription->payment_status = 'pending';
    //         $subscription->save();
    
    //         return response()->json([
    //             'payment_url' => $response->json('payment_url'), // Ссылка для редиректа пользователя
    //         ]);
    //     }
    
    //     return response()->json(['error' => 'Ошибка при создании платежа'], 500);
    // }

    // public function handlePaymentCallback(Request $request)
    // {
    //     $paymentId = $request->input('payment_id');
    //     $status = $request->input('status');

    //     $subscription = Subscription::where('payment_id', $paymentId)->first();

    //     if ($subscription) {
    //         $subscription->payment_status = $status;
    //         $subscription->payment_date = now();
    //         $subscription->save();

    //         if ($status === 'success') {
    //             // Активировать подписку
    //             $subscription->update(['active' => true]);
    //         }

    //         return response()->json(['message' => 'Статус платежа обновлен']);
    //     }

    //     return response()->json(['error' => 'Подписка не найдена'], 404);
    // }

    // private function calculateAmount($type, $duration)
    // {
    //     $prices = [
    //         'buyer' => [
    //             '3 months' => 100000,
    //             '6 months' => 180000,
    //             '1 year' => 300000,
    //             '3 years' => 800000,
    //         ],
    //         'seller' => [
    //             '3 months' => 150000,
    //             '6 months' => 270000,
    //             '1 year' => 500000,
    //             '3 years' => 1200000,
    //         ],
    //         'student' => [
    //             '3 months' => 50000,
    //             '6 months' => 90000,
    //             '1 year' => 150000,
    //             '3 years' => 400000,
    //         ],
    //     ];
    
    //     return $prices[$type][$duration] ?? 0;
    // }


}
