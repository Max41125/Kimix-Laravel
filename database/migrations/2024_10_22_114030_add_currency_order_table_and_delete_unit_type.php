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
        Schema::table('orders', function (Blueprint $table) {
            // Добавляем столбец currency с ограниченными значениями
            $table->enum('currency', ['RUB', 'USD', 'EUR', 'CNY'])->after('total_price');
            
            // Удаляем столбец unit_type
            $table->dropColumn('unit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Удаляем столбец currency
            $table->dropColumn('currency');
            
            // Восстанавливаем столбец unit_type (если необходимо)
            $table->string('unit_type')->nullable();
        });
    }
};
