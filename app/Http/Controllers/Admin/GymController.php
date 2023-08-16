<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use Illuminate\Http\JsonResponse;

class GymController extends Controller
{
    // вывод всех спортзалов
    public function getAllGyms() : JsonResponse{
        return response()->json(Gym::all());
    }
}
