<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateChemicalsTableForTextFields extends Migration
{
    public function up()
    {
        Schema::table('chemicals', function (Blueprint $table) {
            $table->text('image')->change(); // Изменяем тип на TEXT для поля image
            $table->text('description')->change(); // Изменяем тип на TEXT для поля description
        });
    }

    public function down()
    {
        Schema::table('chemicals', function (Blueprint $table) {
            $table->string('image', 255)->change(); // Возвращаем обратно до VARCHAR(255)
            $table->string('description', 255)->change(); // Возвращаем обратно до VARCHAR(255)
        });
    }
}
