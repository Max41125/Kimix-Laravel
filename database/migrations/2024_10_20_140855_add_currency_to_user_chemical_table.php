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
        Schema::table('chemical_user', function (Blueprint $table) {
            $table->string('currency', 3)->nullable()->after('price'); // Добавьте поле currency
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chemical_user', function (Blueprint $table) {
            $table->dropColumn('currency'); // Удалите поле currency
        });
    }
};
