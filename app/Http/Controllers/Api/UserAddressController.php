<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Models\User;

class UserAddressController extends Controller
{
    /**
     * Сохранить или обновить адрес пользователя.
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'city' => 'required|string',
            'street' => 'required|string',
            'house' => 'required|string',
            'building' => 'nullable|string',
            'office' => 'nullable|string',
            'phone' => 'required|string',
            'inn' => 'nullable|string',
            'buyer_fullname' => 'nullable|string',
        ]);

        $address = UserAddress::updateOrCreate(
            ['user_id' => $request->user_id],
            $request->only(['city', 'street', 'house', 'building', 'office', 'phone', 'inn', 'buyer_fullname'])
        );

        return response()->json(['message' => 'Данные успешно сохранены', 'address' => $address], 200);
    }

    /**
     * Получить данные пользователя по ID.
     */
    public function getByUserId($userId)
    {
        $address = UserAddress::where('user_id', $userId)->first();

        if (!$address) {
            return response()->json(['message' => 'Данные пользователя не найдены'], 404);
        }

        return response()->json($address, 200);
    }

    /**
     * Удалить данные пользователя по ID.
     */
    public function deleteByUserId($userId)
    {
        $address = UserAddress::where('user_id', $userId)->first();

        if (!$address) {
            return response()->json(['message' => 'Данные пользователя не найдены'], 404);
        }

        $address->delete();

        return response()->json(['message' => 'Данные пользователя удалены'], 200);
    }
}
