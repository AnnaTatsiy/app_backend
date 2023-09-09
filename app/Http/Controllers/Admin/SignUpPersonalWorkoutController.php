<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Utils;
use App\Models\Coach;
use App\Models\Customer;
use App\Models\GroupWorkout;
use App\Models\PersonalSchedule;
use App\Models\Schedule;
use App\Models\SignUpPersonalWorkout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignUpPersonalWorkoutController extends Controller
{
    // получить все записи на персональные тренировки
    public function signUpPersonalWorkouts(): JsonResponse{
        return response()->json(SignUpPersonalWorkout::with('schedule.coach', 'customer')
            ->orderByDesc('date_begin')
            ->paginate(14));
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

        $workouts = SignUpPersonalWorkout::with('schedule')->get();

        //тренировки на которые был записан клиент
        if($customer != null){
            $customer = Customer::all()->where('passport', $customer)->first();
            $id_customer = ($customer === null) ? 0 : $customer->id;
            $workouts = $workouts->where('customer_id', $id_customer);
        }

        if($coach != null){
            $coach = Coach::all()->where('passport', $coach)->first();
            $id_coach = ($coach === null) ? 0 : $coach->id;
            $workouts = $workouts->where('schedule.coach_id', $id_coach);
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
            $workouts = SignUpPersonalWorkout::with('schedule.coach', 'customer')
                ->whereIn('id', $workouts)
                ->orderByDesc('date_begin')
                ->paginate(14);
        }

        return response()->json($workouts);
    }

    // найти все(за последние 3 недели) записи на персональные тренеровки для заданного тренера
    // вывод постраничный
    // 1 страница - прошлая неделя
    // 2 страница - текущая неделя
    // 3 страница - следующая неделя
    public function getSignUpPersonalWorkoutsByCoach($id, $page): JsonResponse{

        $now = date("Y-m-d");

        // 3 недели - 21 день, нужно вытащить записи за 21 день
        //найдем min и max дату за которые нужно найти записи

        //узнаем день недели текущей даты
        $day = date('w', strtotime($now));
        //так как нумерация дней недели в php начинается с воскресения
        $day = ($day == 0) ? 7 : $day;

        $count_prev = 6 + $day; //кол-во дней от пт первой недели до текущей даты включительно
        $count_next = 14 - $day; //кол-во дней от вск последней недели до текущей даты НЕ включительно

        // в зависимости от недели назначаем дату
        switch ($page){
            case 1:
                $min = date("Y-m-d", strtotime($now . " -{$count_prev} day"));
                $max = date("Y-m-d", strtotime($now . " -{$day} day"));
                break;
            case 2:
                $count_prev =  --$day;
                $count_next =  6 - $day;
                $min = date("Y-m-d", strtotime($now . " -{$count_prev} day"));
                $max = date("Y-m-d", strtotime($now . " +{$count_next} day"));
                break;
            case 3:
                $count_prev = 8 - $day;
                $min = date("Y-m-d", strtotime($now . " +{$count_prev} day"));
                $max = date("Y-m-d", strtotime($now . " +{$count_next} day"));
                break;
        }

        //Нашли все тренеровки заданного тренера
        $workouts = SignUpPersonalWorkout::with('schedule')
            ->get()
            ->where('schedule.coach_id', $id)
            ->whereBetween('date_begin',[$min, $max]);

        if($workouts->count() != 0){
            $workouts = $workouts->pluck('id');
            $workouts = SignUpPersonalWorkout::with('schedule', 'customer')
                ->whereIn('id', $workouts)
                ->orderBy('date_begin')->get();
        }

        return response()->json($workouts);
    }

    // добавляем записи о персональных тренеровках для каждого тренера
    public static function addSignUpPersonalWorkoutsForAllCoaches():void{

        // узнали количество тренеров
        $count = Coach::all()->count();

        //для каждого тренера добавляем записи
        if($count !== 0){
            $coaches = Coach::all();

            foreach ($coaches as $coach){
                SignUpPersonalWorkoutController:: addSignUpPersonalWorkouts($coach->id);
            }
        }
    }

    //добавление записей о персональных тренеровках из расписания тренера
    // $id - id тренера
    private static function addSignUpPersonalWorkouts($id) : void {
        // признак первого запуска
        // берем только записи заданного тренера
        $first = (SignUpPersonalWorkout::with('schedule')->get()
                ->where('schedule.coach_id', $id)
                ->count() == 0);

        $now = date("Y-m-d");

        // находим мах дату - если запуск не первый
        // берем только записи заданного тренера
        if(!$first) $max_date = SignUpPersonalWorkout::with('schedule')->get()
            ->where('schedule.coach_id', $id)
            ->max('date_begin');

        //если первый запуск (max даты нет заполняем на 2 нед)
        //вычисляем кол-во дней которые нужно добавить
        $count = Utils::getCountDaysForAdditionWorkouts($first, $now, $max_date);

        //дата за которую будем добавлять тренировки
        $date = ($first) ? $now :  date("Y-m-d", strtotime($max_date. ' +1 day'));

        //проверка нужно ли добавлять тренировки
        //если от текущей даты до max даты в таблице должно пройти меньше 2 недель - добавляем записи
        if($count > 0 || $first) {
            //цикл по кол-ву дней
            for($i = 0; $i < $count; $i++){
                //узнаем день недели текущей даты - если первый запуск
                //                   след дата после макс даты - если не первый запуск
                $day = date('w', strtotime($date));

                //так как нумерация дней недели в php начинается с воскресения
                $day = ($day == 0) ? 7 : $day;

                //вытаскиваем все расписание заданного тренера за день недели
                $schedules = PersonalSchedule::all()
                    ->where('coach_id', $id)
                    ->where('day_id', $day);

                //если на этот день недели есть расписание то заходим в цикл
                if(count($schedules) != 0){
                    //цикл по расписанию за день недели
                    //добавление записи в таблицу
                    foreach ($schedules as $schedule){
                        $workout = new SignUpPersonalWorkout();
                        $workout->date_begin = $date;
                        $workout->schedule_id = $schedule->id;

                        $workout->save();
                    }//foreach
                }//if

                //шагаем на следующую дату
                $date = date("Y-m-d", strtotime($date . ' +1 day'));
            }//for
        }
    }
}
