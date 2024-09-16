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
            $table->string('name')->unique(); // Уникальное имя
            $table->string('formula')->nullable();
            $table->string('title'); // Убрано уникальное ограничение
            $table->string('cas_number')->nullable(); // Убрано уникальное ограничение
            $table->string('molecular_weight')->nullable(); // Убрано уникальное ограничение
            $table->text('image')->nullable(); // Убрано уникальное ограничение
            $table->string('russian_common_name')->nullable(); // Убрано уникальное ограничение
            $table->text('description')->nullable(); // Убрано уникальное ограничение
            $table->text('russian_description')->nullable(); // Убрано уникальное ограничение
            $table->unsignedBigInteger('cid')->unique(); // Уникальный идентификатор для соединения

            $table->timestamps();
        });

        Schema::create('chemical_synonyms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Убрано уникальное ограничение
            $table->string('russian_name'); // Убрано уникальное ограничение
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
