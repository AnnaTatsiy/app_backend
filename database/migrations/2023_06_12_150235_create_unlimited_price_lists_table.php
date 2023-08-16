<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('unlimited_price_lists', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->unsignedInteger('subscription_type_id');
            $table->foreign('subscription_type_id')->references('id')->on('subscription_types');

            $table->unsignedInteger('validity_period');
            $table->unsignedInteger('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlimited_price_lists');
    }
};
