<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parsers\ChemicalParser;

class ParseChemicals extends Command
{
    protected $signature = 'parse:chemicals';
    protected $description = 'Parse chemicals from PubChem and store in the database.';

    public function handle()
    {
        $this->info('Starting the parsing process...');

        $parser = new ChemicalParser($this); // Передаем текущий объект Command
        $totalChemicals = 3000000; // Предположим, что мы парсим 3000000 веществ
        $this->info("Total chemicals to parse: {$totalChemicals}");

        $parser->parseAll(); // Вызываем метод парсинга

        $this->info('Parsing process completed.');
    }
}
