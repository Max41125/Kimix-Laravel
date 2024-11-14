<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        {
            Schema::table('chemical_synonyms', function (Blueprint $table) {
                // Удаляем текущий внешний ключ
                $table->dropForeign(['cid']);
    
                // Добавляем новый внешний ключ, связывающий cid с chemicals.cid
                $table->foreign('cid')
                      ->references('cid')->on('chemicals')
                      ->onDelete('cascade')
                      ->onUpdate('no action');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chemical_synonyms', function (Blueprint $table) {
            // Удаляем новый внешний ключ
            $table->dropForeign(['cid']);

            // Восстанавливаем старый внешний ключ (связь с chemicals.id)
            $table->foreign('cid')
                  ->references('id')->on('chemicals')
                  ->onDelete('cascade')
                  ->onUpdate('no action');
        });
    }
};
