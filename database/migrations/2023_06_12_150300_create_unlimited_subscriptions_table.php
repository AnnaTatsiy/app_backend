<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlimited_subscriptions', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->unsignedInteger('unlimited_price_list_id');
            $table->foreign('unlimited_price_list_id')->references('id')->on('unlimited_price_lists');

            $table->date("open");//Дата оформления

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlimited_subscriptions');
    }
};
