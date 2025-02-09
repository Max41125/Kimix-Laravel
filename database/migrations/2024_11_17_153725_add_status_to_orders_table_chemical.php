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
        Schema::table('chemical_order', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->default(1)->after('unit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chemical_order', function (Blueprint $table) {
            $table->dropColumn('quantity'); // Удаляем колонку при откате
        });
    }
};
