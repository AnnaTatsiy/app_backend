<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('subscription_types', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->string('title',45);
            $table->boolean("spa")->default(false);
            $table->boolean("pool")->default(false);
            $table->boolean("group")->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_types');
    }
};
