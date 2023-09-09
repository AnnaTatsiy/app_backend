<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_schedules', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->unsignedInteger('day_id'); //день недели
            $table->foreign('day_id')->references('id')->on('days');

            $table->time("time_begin");//Время начала

            $table->unsignedInteger('coach_id');
            $table->foreign('coach_id')->references('id')->on('coaches');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_schedules');
    }
};
