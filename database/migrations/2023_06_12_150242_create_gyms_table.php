<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gyms', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->string('title',45);
            $table->unsignedInteger('capacity'); //макс кол-во человек, которое вмещает зал

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gyms');
    }
};
