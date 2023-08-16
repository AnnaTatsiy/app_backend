<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    // вывести расписание групповых тренировок
    // id - день недели
    public function schedules($id): JsonResponse{
        return response()->json(Schedule::with('day', 'gym', 'coach', 'workout_type')->where('day_id','=', $id)->get());
    }

    // вывести расписание групповых тренировок
    public function schedulesGetAll(): JsonResponse{
        return response()->json(Schedule::with('day', 'gym', 'coach', 'workout_type')->get());
    }
}
