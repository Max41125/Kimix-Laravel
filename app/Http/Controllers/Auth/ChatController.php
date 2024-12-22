<?php 

namespace App\Http\Controllers\Auth;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Document;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'message' => 'required|string',
            'user_id' => 'required|integer',
            'order_id' => 'required|string',
            'username' => 'required|string',
        ]);

        $message = $request->input('message');
        $userId = $request->input('user_id');
        $orderId = $request->input('order_id'); // Get order_id from the request
        $username = $request->input('username'); // Get username from the request

        Log::info('Sending message', ['message' => $message, 'user_id' => $userId, 'order_id' => $orderId]);
        event(new MessageSent($message, $userId, $orderId, $username));

        return response()->json(['success' => true]);
    }

    public function getMessages($orderId)
    {
        // Получаем сообщения с загруженными данными о пользователях
        $messages = Message::with('user') // Загружаем связанные данные о пользователе
            ->where('order_id', $orderId)
            ->get();
    
        // Преобразуем коллекцию сообщений в массив с необходимыми полями
        $messagesWithUsernames = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'user_id' => $message->user_id,
                'username' => $message->user ? $message->user->name : null, // Получаем имя пользователя
                'order_id' => $message->order_id,
            ];
        });
    
        return response()->json($messagesWithUsernames); // Возвращаем сообщения в формате JSON
    }
   

    public function uploadDocument(Request $request)
    {
        // Валидация данных: разрешаем docx, jpeg, pdf, doc
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,jpeg,jpg|max:10240', // Максимум 10MB
            'order_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);
    
        // Логируем MIME тип файла для отладки
        $mimeType = $request->file('file')->getMimeType();
        Log::info("Загружаемый файл имеет MIME тип: {$mimeType}");
    
        // Проверка MIME типа файла
        $allowedMimeTypes = [
            'application/pdf', // PDF
            'application/msword', // DOC
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
            'image/jpeg', // JPEG
        ];
    
        if (!in_array($mimeType, $allowedMimeTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный тип файла. Разрешены только PDF, DOC, DOCX, и JPEG.',
            ], 400);
        }
    
        // Сохраняем файл в 'public/documents', чтобы получить корректный публичный путь
        try {
            $path = $request->file('file')->store('public/documents');
        } catch (\Exception $e) {
            Log::error('Ошибка при сохранении файла: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке файла. Пожалуйста, попробуйте позже.',
            ], 500);
        }
    
        // Преобразуем путь для публичного доступа
        $publicPath = '/storage' . str_replace('public', '', $path);
    
        // Сохраняем информацию о файле в базе данных
        try {
            $document = Document::create([
                'user_id' => $request->input('user_id'),
                'order_id' => $request->input('order_id'),
                'filename' => $request->file('file')->getClientOriginalName(),
                'path' => $publicPath, // Сохраняем публичный путь
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при сохранении информации о файле в базе данных: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении данных. Пожалуйста, попробуйте позже.',
            ], 500);
        }
    
        // Возвращаем успешный ответ
        return response()->json([
            'success' => true,
            'document' => $document,
        ]);
    }
    
    
    

    public function getDocuments($orderId)
    {
        // Получаем документы, связанные с указанным заказом
        $documents = Document::where('order_id', $orderId)->get();

        return response()->json($documents);
    }


}
