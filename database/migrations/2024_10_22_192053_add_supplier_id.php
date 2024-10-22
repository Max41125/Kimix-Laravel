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
            $table->unsignedBigInteger('supplier_id')->nullable()->after('order_id'); // Поле допускает NULL
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chemical_order', function (Blueprint $table) {
            //
        });
    }
};
