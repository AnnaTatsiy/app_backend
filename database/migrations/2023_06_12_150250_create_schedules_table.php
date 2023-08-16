<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->unsignedInteger('day_id');
            $table->foreign('day_id')->references('id')->on('days');

            $table->unsignedInteger('gym_id');
            $table->foreign('gym_id')->references('id')->on('gyms');

            $table->time("time_begin");//Время начала
            $table->time("time_end");//Время окончания

            $table->unsignedInteger('coach_id');
            $table->foreign('coach_id')->references('id')->on('coaches');

            $table->unsignedInteger('workout_type_id');
            $table->foreign('workout_type_id')->references('id')->on('workout_types');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
