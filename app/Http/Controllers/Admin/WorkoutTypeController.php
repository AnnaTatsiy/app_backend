<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkoutType;
use Illuminate\Http\JsonResponse;

class WorkoutTypeController extends Controller
{
    public function getAllWorkoutTypes(): JsonResponse{
        return response()->json(WorkoutType::all());
    }

}
