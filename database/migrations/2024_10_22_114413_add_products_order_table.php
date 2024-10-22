<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Сделать столбец nullable, чтобы он мог принимать null значения
            $table->json('products')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Восстановить NOT NULL для столбца
            $table->json('products')->nullable(false)->change();
        });
    }
};
