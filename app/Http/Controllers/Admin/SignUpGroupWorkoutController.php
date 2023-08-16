<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SignUpGroupWorkout;
use Illuminate\Http\JsonResponse;

class SignUpGroupWorkoutController extends Controller
{
    // получить все записи на групповые тренировки
    public function signUpGroupWorkouts(): JsonResponse{
        return response()->json(SignUpGroupWorkout::with('customer', 'group_workout')->get());
    }

    //получить все записи на групповую тренировку по id тренировки
    public function selectSignUpGroupWorkoutsByWorkoutId($id): JsonResponse{
        return response()->json(SignUpGroupWorkout::with('customer')->where('group_workout_id','=',$id)->get());
    }
}
