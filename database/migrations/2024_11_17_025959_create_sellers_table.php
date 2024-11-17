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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // ИП или ООО
            $table->string('full_name');
            $table->string('short_name')->nullable();
            $table->string('legal_address');
            $table->string('actual_address');
            $table->string('email');
            $table->string('phone');
            $table->string('inn')->unique();
            $table->string('kpp')->nullable();
            $table->string('ogrn');
            $table->string('director'); // Должность и ФИО
            $table->string('chief_accountant')->nullable();
            $table->string('authorized_person')->nullable();
            $table->string('bank_name');
            $table->string('bik');
            $table->string('corr_account');
            $table->string('settlement_account');
            $table->string('okved')->nullable();
            $table->string('tax_system');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
