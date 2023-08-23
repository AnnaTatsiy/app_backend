<?php

namespace App\Console;

use App\Http\Controllers\Admin\GroupWorkoutController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function (){
            GroupWorkoutController::preparationEdit();
        })->timezone('Europe/Moscow')->dailyAt('15:00'); // ежедневно в 15:00

        $schedule->call(function (){
            GroupWorkoutController:: preparationAdd();
        })->timezone('Europe/Moscow')->dailyAt('15:00'); // ежедневно в 15:00
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
