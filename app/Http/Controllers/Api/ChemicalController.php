<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chemical;
use App\Models\Subscription;
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
        $chemical = Chemical::with('chemicalSynonyms') // подгружаем синонимы
        ->find($id);

        if ($chemical) {
            // Возвращаем данные химического вещества и синонимы
            return response()->json([
                'chemical' => $chemical,
                'synonyms' => $chemical->chemicalSynonyms, // синонимы
            ]);
        }
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
    // Поиск химических веществ
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
    
        if (!$searchTerm) {
            return response()->json(['message' => 'No search term provided'], 400);
        }
    
        // Логируем полученный запрос для отладки
        \Log::info("Search term: {$searchTerm}");
    
        // Если запрос не начинается с 'InChI=', добавляем его для поиска по inchi
        if (strpos(strtolower($searchTerm), 'inchi=') !== 0) {
            $searchTerm2 = 'InChI=' . $searchTerm;
        } else {
            $searchTerm2 = $searchTerm;
        }
    
        // Выполняем точный поиск в таблице chemicals
        $query = Chemical::query();
    
        $query->where(function ($q) use ($searchTerm, $searchTerm2) {
            $q->whereRaw('LOWER(title) = LOWER(?)', [$searchTerm])
                ->orWhereRaw('LOWER(name) = LOWER(?)', [$searchTerm])
                ->orWhereRaw('LOWER(cas_number) = LOWER(?)', [$searchTerm])
                ->orWhereRaw('LOWER(formula) = LOWER(?)', [$searchTerm])
                ->orWhereRaw('LOWER(russian_common_name) = LOWER(?)', [$searchTerm])
                ->orWhereRaw('LOWER(inchi) = LOWER(?)', [$searchTerm2])  // Поиск по inchi с префиксом
                ->orWhereRaw('LOWER(smiles) = LOWER(?)', [$searchTerm]);
        });
    
        // Добавляем точный поиск по синонимам (таблица chemical_synonyms)
        $query->orWhereHas('chemicalSynonyms', function ($q) use ($searchTerm) {
            $q->whereRaw('LOWER(name) = LOWER(?)', [$searchTerm])
                ->orWhereRaw('LOWER(russian_name) = LOWER(?)', [$searchTerm]);
        });
    
        // Выполнение запроса
        $chemicals = $query->get();
    
        return response()->json($chemicals);
    }
    
    public function checkSuppliersExistence($chemicalId)
    {
        // Находим химическое вещество по ID
        $chemical = Chemical::findOrFail($chemicalId);
        
        // Проверяем, есть ли поставщики
        $hasSuppliers = $chemical->users()->exists();
        
        return response()->json(['has_suppliers' => $hasSuppliers], 200);
    }
    

    public function getSuppliersByChemicalId($chemicalId, $userId)
    {
        // Проверяем, есть ли подписка у пользователя
        $subscription = Subscription::where('user_id', $userId)
            ->where('chemical_id', $chemicalId)
            ->first();
    
        if (!$subscription) {
            return response()->json(['error' => 'No subscription found for this chemical.'], 403);
        }
    
        // Проверяем, не закончилась ли подписка
        if ($subscription->end_date < now()) {
            return response()->json(['error' => 'Subscription has expired.'], 403);
        }
    
        // Находим химическое вещество по ID
        $chemical = Chemical::findOrFail($chemicalId);
        
        // Получаем поставщиков с данными из таблицы pivot (chemical_user)
        $suppliers = $chemical->users()->select('users.id', 'users.name', 'chemical_user.unit_type', 'chemical_user.price', 'chemical_user.currency')
            ->get();
        
        return response()->json($suppliers, 200);
    }
    
    
    
    

}

