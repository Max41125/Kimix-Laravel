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
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('formula')->nullable();
            $table->string('title')->unique();
            $table->string('cas_number')->unique();
            $table->string('molecular_weight')->unique();
            $table->string('image')->unique();
            $table->string('russian_common_name')->unique();
            $table->string('description')->unique();
            $table->unsignedBigInteger('cid')->unique(); // Уникальный идентификатор для соединения

            $table->timestamps();
        });


        Schema::create('chemical_synonyms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Уникальный CAS номер
            $table->unsignedBigInteger('cid'); // Убедись, что это unsignedBigInteger
            $table->foreign('cid')->references('id')->on('chemicals')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemical_synonyms');
        Schema::dropIfExists('chemicals');
       
    }
};
