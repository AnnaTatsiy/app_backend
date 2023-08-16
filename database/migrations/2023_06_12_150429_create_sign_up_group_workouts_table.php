<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sign_up_group_workouts', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->unsignedInteger('group_workout_id');
            $table->foreign('group_workout_id')->references('id')->on('group_workouts');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sign_up_group_workouts');
    }
};
