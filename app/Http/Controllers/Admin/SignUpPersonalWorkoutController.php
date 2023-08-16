<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Customer;
use App\Models\SignUpPersonalWorkout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignUpPersonalWorkoutController extends Controller
{
    // получить все записи на персональные тренировки
    public function signUpPersonalWorkouts(): JsonResponse{
        return response()->json(SignUpPersonalWorkout::with('customer', 'coach')->orderByDesc('date_begin')->orderByDesc('time_begin')->paginate(14));
    }

    //получить все тренировки пройденные через фильтр
    public function signUpPersonalWorkoutsFiltered(Request $request) : JsonResponse{

        $date_beg = $request->input('date_beg');
        $date_end = $request->input('date_end');
        $coach = $request->input('coach');
        $customer = $request->input('customer');

        $date_beg = ($date_beg == "") ? null: $date_beg;
        $date_end = ($date_end == "") ? null: $date_end;
        $coach = ($coach == "") ? null: $coach;
        $customer = ($customer == "") ? null: $customer;

        $workouts = SignUpPersonalWorkout::all();

        //тренировки на которые был записан клиент
        if($customer != null){
            $customer = Customer::all()->where('passport', $customer)->first();
            $workouts = $workouts->where('customer_id', $customer->id);
        }

        if($coach != null){
            $coach = Coach::all()->where('passport', $coach)->first();
            $workouts = $workouts->where('coach_id', $coach->id);
        }

        $workouts = match (true) {
            $date_beg == null && $date_end == null => $workouts,
            $date_beg != null && $date_end == null => $workouts->where('date_begin', $date_beg),
            ($date_beg != null && $date_end != null) && ($date_beg < $date_end) => $workouts->whereBetween('date_begin', [$date_beg, $date_end]),
            ($date_beg != null && $date_end != null) && ($date_beg > $date_end) => $workouts->whereBetween('date_begin', [$date_end, $date_beg]),
            default => $workouts->where('date_begin', $date_end)
        };

        if($workouts->count() != 0){
            $workouts = $workouts->pluck('id');
            $workouts = SignUpPersonalWorkout::with('customer', 'coach')
                ->whereIn('id', $workouts)
                ->orderByDesc('date_begin')
                ->orderByDesc('time_begin')
                ->paginate(14);
        }

        return response()->json($workouts);
    }
}
