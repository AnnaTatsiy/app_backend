<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->string('title',2)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
