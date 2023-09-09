<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sign_up_personal_workouts', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->date("date_begin");//Дата

            $table->unsignedInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->unsignedInteger('schedule_id')->nullable();
            $table->foreign('schedule_id')->references('id')->on('personal_schedules');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sign_up_personal_workouts');
    }
};
