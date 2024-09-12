<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chemical;
use Illuminate\Http\Request;

class ChemicalController extends Controller
{
    // Получение всех записей
    public function index()
    {
        $chemicals = Chemical::all();
        return response()->json($chemicals);
    }

    // Получение одной записи по ID
    public function show($id)
    {
        $chemical = Chemical::find($id);

        if ($chemical) {
            return response()->json($chemical);
        }

        return response()->json(['message' => 'Chemical not found'], 404);
    }

    // Добавление нового химического вещества
    public function store(Request $request)
    {
        $chemical = Chemical::create($request->all());
        return response()->json($chemical, 201);
    }

    // Обновление записи
    public function update(Request $request, $id)
    {
        $chemical = Chemical::find($id);

        if ($chemical) {
            $chemical->update($request->all());
            return response()->json($chemical);
        }

        return response()->json(['message' => 'Chemical not found'], 404);
    }

    // Удаление записи
    public function destroy($id)
    {
        $chemical = Chemical::find($id);

        if ($chemical) {
            $chemical->delete();
            return response()->json(['message' => 'Chemical deleted']);
        }

        return response()->json(['message' => 'Chemical not found'], 404);
    }
}

