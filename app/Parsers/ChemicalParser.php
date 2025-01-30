<?php

namespace App\Parsers;

use GuzzleHttp\Client;
use App\Models\Chemical;
use App\Models\CasNumber;
use App\Models\ChemicalSynonym; 
use App\Models\ParsingProgress;
use Exception;
use Illuminate\Console\Command;

class ChemicalParser
{
    protected $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function parseAll()
    {
        $client = new Client();
       
        $endCID = 3000000; // Конечный CID
        $pageSize = 100; // Количество записей на странице
        $maxRetries = 3;
        $startCID = (int) ParsingProgress::where('key', 'last_processed_cid')->value('value') ?? 11421;
        while ($startCID <= $endCID) { // Цикл по диапазону CID

            // Обработка ошибок при попытке получения свойств соединений
            if ($startCID === 0) {
                $startCID = 11421;
            }

            $cidRange = implode(',', range($startCID, min($startCID + $pageSize - 1, $endCID)));
            $this->command->info("START CIDs: {$startCID}"); // Отладочное сообщение
            $this->command->info("Fetching CIDs: {$cidRange}"); // Отладочное сообщение
    
            // Используем API-метод для получения свойств соединений
            $response = $client->get("https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/{$cidRange}/property/MolecularFormula,IUPACName,Title,MolecularWeight,InChIKey,InChI,CanonicalSMILES,IsomericSMILES/JSON");
    
            if ($response->getStatusCode() == 200) {
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);
    
                // Проверяем, что данные о соединениях существуют
                if (isset($data['PropertyTable']['Properties']) && !empty($data['PropertyTable']['Properties'])) {
                    foreach ($data['PropertyTable']['Properties'] as $compound) {
                        // Извлекаем свойства
                        $name = $compound['IUPACName'] ?? null; // Получаем IUPAC имя
                        $formula = $compound['MolecularFormula'] ?? null; // Получаем молекулярную формулу
                        $title = $compound['Title'] ?? null; // Получаем заголовок
                        $molecularWeight = $compound['MolecularWeight'] ?? null; // Получаем молекулярный вес
                        $InChI = $compound['InChI'] ?? null; // Получаем InChI
                        $cid = $compound['CID'] ?? null; // Получаем CID
                        $CanonicalSMILES = $compound['CanonicalSMILES'] ?? null; //Получаем Smiles
                        // Получаем CAS номер и изображение по InChI
                        $casData = $this->fetchCasNumbersByInChI($client, $InChI);
                        $casNumber = $casData['cas_number'] ?? null; // Устанавливаем значение по умолчанию
                        $image = $casData['image'] ?? ''; // Устанавливаем значение по умолчанию
    
                        // Получаем описание по CID
                        $description = $this->fetchDescriptionByCID($client, $cid);
                        $russianCommonName = $this->fetchRussianCommonName($title);
                        $russianDescription = $this->fetchRussianDescription($description);
                        
                        // Проверяем, что имя и CID не пустые
                        if ($name && $cid) {
                            try {
                                $chemical = Chemical::updateOrCreate(
                                    ['name' => $name],
                                    [
                                        'formula' => $formula,
                                        'cid' => $cid,
                                        'cas_number' => $casNumber ?: null, // Устанавливаем в null, если CAS номер пуст
                                        'title' => $title ?: null,
                                        'molecular_weight' => $molecularWeight,
                                        'image' => $image,
                                        'russian_common_name' => $russianCommonName ?: null,
                                        'description' => $description,
                                        'russian_description' => $russianDescription,
                                        'inchi' => $InChI,
                                        'smiles' => $CanonicalSMILES,
                                    ]
                                );
                               
                                $chemical = $chemical->fresh();
                                if ($chemical && $chemical->cid === $cid) {
                                    $this->command->info("Запись для CID {$cid} найдена в chemicals.");
                                    $this->fetchChemicalSynonyms($client, $cid, $maxRetries);
                                } else {
                                    $this->command->error("Не удалось подтвердить наличие вещества с CID: {$cid}");
                                }

                                $this->command->info("Добавлено вещество: {$name} (CID: {$cid}, CAS: {$casNumber})");
    
                            } catch (\Exception $e) {
                                $this->command->error("Ошибка при добавлении химического вещества: " . $e->getMessage());
                            }
                        } else {
                            if (!$cid) {
                                $this->command->error("CID не найден для соединения.");
                            }
                            if (!$name) {
                                $this->command->error("Имя не найдено для соединения.");
                            }
                        }
                    }
                } else {
                    $this->command->error("Не удалось получить соединения из API.");
                }
            } else {
                $this->command->error("Ошибка при обращении к API: " . $response->getStatusCode());
            }
            ParsingProgress::updateOrCreate(
                ['key' => 'last_processed_cid'],
                ['value' => (string) $startCID]
            );
            
            $startCID += $pageSize; // Переход к следующему диапазону CID
        }
    }

    private function fetchCasNumbersByInChI($client, $InChI)
    {
        $baseUrl = 'https://commonchemistry.cas.org/api/search';
    
        if (empty($InChI)) {
            $this->command->error("Ошибка: InChI пуст.");
            return ['cas_number' => null, 'image' => null];
        }
    
        try {
            $response = $client->get($baseUrl, [
                'query' => ['q' => $InChI]
            ]);
    
            if ($response->getStatusCode() == 200) {
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);
    
                if (!empty($data['results'][0]['rn'])) {
                    return [
                        'cas_number' => $data['results'][0]['rn'],
                        'image' => $data['results'][0]['image'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error("Ошибка CAS API: " . $e->getMessage());
        }
    
        return ['cas_number' => null, 'image' => null];
    }

    private function fetchChemicalSynonyms($client, $cid, $maxRetries)
    {
        $attempts = 0;

        while ($attempts < $maxRetries) {
            try {
                $synonymsResponse = $client->get("https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/{$cid}/synonyms/JSON");

                if ($synonymsResponse->getStatusCode() == 200) {
                    $synonymsBody = $synonymsResponse->getBody()->getContents();
                    $synonymsData = json_decode($synonymsBody, true);

                    if (isset($synonymsData['InformationList']['Information'][0]['Synonym'])) {
                        $synonyms = $synonymsData['InformationList']['Information'][0]['Synonym'];

                        // Сохраняем синонимы и их переводы
                        foreach ($synonyms as $synonym) {
                            // Переводим синоним с помощью Google Translate
                            $translatedSynonym = $this->translateSynonym($synonym);
                            
                            if (Chemical::where('cid', $cid)->exists()) {

                               
                                
                                ChemicalSynonym::updateOrCreate(
                                    ['cid' => $cid, 'name' => $synonym, 'russian_name' => $translatedSynonym],
                                 
                                );
                            } else {
                                $this->command->error("Соединение с CID: {$cid} отсутствует в таблице chemicals, синонимы не будут добавлены.");
                            }

                            $this->command->info("Добавлен синоним: {$synonym} (перевод: {$translatedSynonym}) для CID: {$cid}");
                        }
                        return; // Выходим из функции, если успешный запрос выполнен
                    } else {
                        $this->command->error("Синонимы не найдены для CID: {$cid}");
                        return;
                    }
                } elseif ($synonymsResponse->getStatusCode() == 404) {
                    $attempts++;
                    $this->command->info("Попытка {$attempts} для CID: {$cid} завершилась 404. Повторяем...");
                } else {
                    $this->command->error("Не удалось получить синонимы для CID: {$cid} с кодом статуса: " . $synonymsResponse->getStatusCode());
                    return;
                }
            } catch (Exception $e) {
                $this->command->error("Ошибка при получении синонимов: " . $e->getMessage());
                return;
            }
        }

        $this->command->info("Не удалось получить синонимы для CID: {$cid} после {$maxRetries} попыток.");
    }

    private function translateSynonym($text)
    {
        // URL для бесплатного API перевода
        $url = "https://ftapi.pythonanywhere.com/translate?sl=en&dl=ru&text=" . urlencode($text);
    
        // Инициализация HTTP-клиента
        $client = new Client([
            'timeout' => 10, // Таймаут в секундах
        ]);
    
        try {
            // Отправка GET-запроса
            $response = $client->get($url);
    
            // Проверяем успешность запроса
            if ($response->getStatusCode() == 200) {
                // Парсим JSON-ответ
                $data = json_decode($response->getBody()->getContents(), true);
    
                // Извлекаем перевод из 'destination-text'
                if (isset($data['destination-text'])) {
                    return $data['destination-text']; // Возвращаем перевод
                } else {
                    return $text; // Если перевод не найден, возвращаем оригинал
                }
            } else {
                return $text; // Если ошибка в запросе, возвращаем оригинал
            }
        } catch (Exception $e) {
            // Логируем ошибку и возвращаем оригинал текста
            $this->command->error("Ошибка перевода: " . $e->getMessage());
            return $text;
        }
    }

    // Остальные методы, такие как fetchRussianCommonName, fetchDescriptionByCID, fetchRussianDescription аналогичны и не повторяются здесь для краткости.


    private function fetchDescriptionByCID($client, $cid)
    {
        $url = "https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/{$cid}/description/json";
    
        try {
            $response = $client->get($url);
    
            if ($response->getStatusCode() == 200) {
                $body = json_decode($response->getBody()->getContents(), true);
                foreach ($body['InformationList']['Information'] ?? [] as $info) {
                    if (!empty($info['Description'])) {
                        return $info['Description'];
                    }
                }
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getCode() == 404) {
                $this->command->info("Описание отсутствует для CID: {$cid}");
                return ''; // Возвращаем пустую строку вместо ошибки
            }
        } catch (\Exception $e) {
            \Log::error("Ошибка при запросе описания CID {$cid}: " . $e->getMessage());
        }
    
        return '';
    }
    

    private function fetchRussianCommonName($title)
    {
        return $this->translateSynonym($title);
    }
    
    private function fetchRussianDescription($description)
    {
        return $this->translateSynonym($description);
    }
    




}
