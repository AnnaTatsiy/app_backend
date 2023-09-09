<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_edit_personal_schedules', function (Blueprint $table) {
            $table->increments('id');  // первичный ключ

            $table->date("date_edit");//Дата изменения расписания

            $table->unsignedInteger('coach_id');
            $table->foreign('coach_id')->references('id')->on('coaches');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_edit_personal_schedules');
    }
};

