<?php

namespace App\Parsers;

use GuzzleHttp\Client;
use App\Models\Chemical;
use App\Models\CasNumber;
use App\Models\ChemicalSynonym; 
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
        $startCID = 1; // Начальный CID
        $endCID = 100; // Конечный CID
        $pageSize = 100; // Количество записей на странице
        $maxRetries = 3;
        while ($startCID <= $endCID) { // Цикл по диапазону CID
            $cidRange = implode(',', range($startCID, min($startCID + $pageSize - 1, $endCID)));
            $this->command->info("Fetching CIDs: {$cidRange}"); // Отладочное сообщение
    
            // Используем API-метод для получения свойств соединений
            $response = $client->get("https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/{$cidRange}/property/MolecularFormula,IUPACName,Title,MolecularWeight,InChIKey,InChI/JSON");
    
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
    
                        // Получаем CAS номер и изображение по InChI
                        $casData = $this->fetchCasNumbersByInChI($client, $InChI);
                        $casNumber = $casData['cas_number'] ?? null; // Устанавливаем значение по умолчанию
                        $image = $casData['image'] ?? ''; // Устанавливаем значение по умолчанию
    
                        // Получаем описание по CID
                        $description = $this->fetchDescriptionByCID($client, $cid);
                        $russianCommonName = $this->fetchRussianCommonName($title);
                        
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
                                        'description' => $description
                                    ]
                                );
                                $this->fetchChemicalSynonyms($client, $cid, $maxRetries);

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
    
            $startCID += $pageSize; // Переход к следующему диапазону CID
        }
    }

    private function fetchCasNumbersByInChI($client, $InChI)
    {
        $baseUrl = 'https://commonchemistry.cas.org/api/search';
        $response = $client->get($baseUrl, [
            'query' => [
                'q' => $InChI,
            ]
        ]);
    
        if ($response->getStatusCode() == 200) {
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
    
            if (isset($data['results'][0]['rn'])) {
                $casNumber = $data['results'][0]['rn']; // CAS номер
                $image = $data['results'][0]['image'] ?? null; // Получаем изображение (SVG)
    
                return [
                    'cas_number' => $casNumber,
                    'image' => $image,
                ];
            } else {
                $this->command->info("CAS номер не найден для InChI: {$InChI}");
                return ['cas_number' => null, 'image' => null];
            }
        } else {
            $this->command->info("Не удалось получить CAS номер для InChI: {$InChI} с кодом статуса: " . $response->getStatusCode());
            return ['cas_number' => null, 'image' => null];
        }
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

                        // Сохраняем синонимы в отдельной таблице
                        foreach ($synonyms as $synonym) {
                            ChemicalSynonym::updateOrCreate(
                                ['cid' => $cid, 'name' => $synonym]
                            );
                            $this->command->info("Добавлен синоним: {$synonym} для CID: {$cid}");
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

    private function fetchDescriptionByCID($client, $cid)
    {
        $url = "https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/cid/{$cid}/description/json";
        $response = $client->get($url);
    
        if ($response->getStatusCode() == 200) {
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            $content = $data['InformationList']['Information'];
    
            // Перебираем массив Information, чтобы найти описание
            foreach ($content as $info) {
                if (isset($info['Description'])) {
                    return $info['Description'];
                }
            }
    
            // Если не нашли описание
            $this->command->info("Описание не найдено для CID: {$cid}");
            return '';
        } else {
            $this->command->info("Не удалось получить описание для CID: {$cid} с кодом статуса: " . $response->getStatusCode());
            return '';
        }
    }
    
    private function fetchRussianCommonName($title)
    {
        // Замените пробелы на знак подчеркивания для формата Wikidata
        $formattedTitle = str_replace(' ', '_', $title);
        
        // Запрос к Wikidata API для поиска по названию
        $url = "https://www.wikidata.org/w/api.php?action=wbsearchentities&search={$formattedTitle}&language=ru&format=json";
        $client = new Client();
        $response = $client->get($url);
        
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['search']) && !empty($data['search'])) {
                // Возвращаем первое русскоязычное имя
                return $data['search'][0]['label'];
            }
        }
        
        // Если ничего не найдено, возвращаем null
        return null;
    }
}
