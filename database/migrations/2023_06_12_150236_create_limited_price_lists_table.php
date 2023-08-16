<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('limited_price_lists', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->unsignedInteger('coach_id');
            $table->foreign('coach_id')->references('id')->on('coaches');

            $table->unsignedInteger('amount_workout');
            $table->unsignedInteger('price');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('limited_price_lists');
    }
};
