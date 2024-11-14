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
    // Поиск химических веществ
    public function search(Request $request)
    {
        $searchTerm = $request->input('q');
    
        if (!$searchTerm) {
            return response()->json(['message' => 'No search term provided'], 400);
        }
    
        // Логируем полученный запрос для отладки
        \Log::info("Search term: {$searchTerm}");
    
        // Разбиваем поисковую строку на ключевые слова
        $keywords = explode(' ', $searchTerm);
    
        $query = Chemical::query();
    
        // Для каждого ключевого слова строим условия поиска
        foreach ($keywords as $keyword) {
            $query->orWhere(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(title) LIKE LOWER(?)', ['%' . $keyword . '%'])
                  ->orWhereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $keyword . '%'])
                  ->orWhereRaw('LOWER(cas_number) LIKE LOWER(?)', ['%' . $keyword . '%'])
                  ->orWhereRaw('LOWER(formula) LIKE LOWER(?)', ['%' . $keyword . '%'])
                  ->orWhereRaw('LOWER(russian_common_name) LIKE LOWER(?)', ['%' . $keyword . '%'])
                  ->orWhereRaw('LOWER(inchi) LIKE LOWER(?)', ['%' . $keyword . '%'])
                  ->orWhereRaw('LOWER(smiles) LIKE LOWER(?)', ['%' . $keyword . '%']);
            });
        }
        
        // Добавление синонимов в поиск (поиск по таблице chemical_synonyms)
        $query->orWhereHas('chemicalSynonyms', function ($q) use ($searchTerm) {
            $q->whereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $searchTerm . '%'])
              ->orWhereRaw('LOWER(russian_name) LIKE LOWER(?)', ['%' . $searchTerm . '%']);
        });
        
        // Выполнение запроса
        $chemicals = $query->get();
        
        return response()->json($chemicals);
        
    }
    
    
    public function getSuppliersByChemicalId($chemicalId)
    {
        // Находим химическое вещество по ID
        $chemical = Chemical::findOrFail($chemicalId);
        
        // Получаем поставщиков с данными из таблицы pivot (chemical_user)
        $suppliers = $chemical->users()->select('users.id','users.name', 'chemical_user.unit_type', 'chemical_user.price', 'chemical_user.currency')
            ->get();
        
        return response()->json($suppliers, 200);
    }



}

