<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Utils;
use App\Models\Coach;
use App\Models\Customer;
use App\Models\GroupWorkout;
use App\Models\Schedule;
use App\Models\SignUpGroupWorkout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupWorkoutController extends Controller
{
    //добавдение групповых тренировок происходит из расписания занятий
    public function preparationAdd() : void{

        //признак первого запуска
        $first = (GroupWorkout::all()->count() == 0);

        //находим мах дату - если запуск не первый
        if(!$first) $max_date = GroupWorkout::all()->max('event');

        $now = date("Y-m-d");

        //если первый запуск (max даты нет заполняем на 2 нед)
        //вычисляем кол-во дней которые нужно добавить
        switch (true){

            // первый запуск
            case $first:
                    $count = 14;
                break;

            // если дата текущая больше даты последней тренировки
            case ($now > $max_date):
                    $count = 13 + Utils::subtractingDates($max_date, $now);
                break;

            case ($now == $max_date):
                $count = 13;
                break;

            // если дата текущая меньше даты последней тренировки
            case ($now <  $max_date):
                $count = 10 - Utils::subtractingDates($now, $max_date);
                break;
        }

        //дата за которую будем добавлять тренировки
        $date = ($first) ? $now :  date("Y-m-d", strtotime($max_date. ' +1 day'));

        //проверка нужно ли обновлять таблицу групповые тренеровки
        //если от текущей даты до max даты в таблице должно пройти меньше 2 недель - добавляем записи
        if($count > 0 || $first){

            //цикл по кол-ву дней
            for($i = 0; $i < $count; $i++){

                //узнаем день недели текущей даты - если первый запуск
                //                   след дата после макс даты - если не первый запуск
                $day = date('w', strtotime($date));

                //так как нумерация дней недели в php начинается с ВС
                $day = ($day == 0) ? 7 : $day;

                //вытаскиваем все расписание за день недели
                $schedules = Schedule::all()->where('day_id', $day);

                //если на этот день недели есть расписание то заходим в цикл
                if(count($schedules) != 0){

                    //цикл по расписанию за день недели
                    //добавление записи в таблицу
                    foreach ($schedules as $schedule){

                       $workout = new GroupWorkout();
                       $workout->event = $date;
                       $workout->cancelled = false;
                       $workout->schedule_id = $schedule->id;
                       $workout->reason = "";

                       $workout->save();

                    }//foreach

                }//if

                //шагаем на следующую дату
                $date = date("Y-m-d", strtotime($date . ' +1 day'));

            }//for
        }
    }

    // если на тренировку записались менее 5 человек - тренировка отменяется
    // отменяем тренировки
    public function preparationEdit() : void {
        $workouts = GroupWorkout::all()->where('cancelled', 0)->where('event','<=', date("Y-m-d"));

        if(count($workouts) != 0) {
            foreach ($workouts as $workout) {

                $count = SignUpGroupWorkout::all()->where('group_workout_id', $workout->id)->count();

                if ($count < 5) {
                    $workout->cancelled = 1;
                    $workout->reason = "на тренировку записалось менее 5 человек!";

                    $workout->save();
                }
            }
        }
    }

    //редактирование тренировки - возможна только отмена
    public function groupWorkoutEdit(Request $request) : JsonResponse{

        $workout = GroupWorkout::with( 'schedule.gym','schedule.workout_type', 'schedule.coach','schedule.day')->where('id',  $request->input('id'))->first();
        $workout->cancelled = 1;
        $workout->reason = "тренировка была отменена по заявке тренера!";

        $workout->save();

        return response()->json($workout);
    }

    // получить все записи (вывод всех групповых тренировок)
    public function getGroupWorkouts(): JsonResponse{
        return response()->json(GroupWorkout::with( 'schedule.gym','schedule.workout_type', 'schedule.coach','schedule.day')->orderByDesc('event')->get());
    }

    // получить все записи (вывод всех групповых тренировок) постранично
    public function groupWorkouts(): JsonResponse{
        $this->preparationEdit();
        $this->preparationAdd();
        return response()->json(GroupWorkout::with( 'schedule.gym','schedule.workout_type', 'schedule.coach','schedule.day')->orderByDesc('event')->paginate(12));
    }

    //получить всю информацию о групповой тренировки по id
    public function groupWorkoutById($id): JsonResponse {
        return response()->json(GroupWorkout::with( 'schedule.gym','schedule.workout_type', 'schedule.coach','schedule.day')->where('id',$id)->first());
    }

    //получить все тренировки пройденные через фильтр
    public function groupWorkoutsFiltered(Request $request) : JsonResponse{

        $date_beg = $request->input('date_beg');
        $date_end = $request->input('date_end');
        $coach = $request->input('coach');
        $customer = $request->input('customer');
        $cancelled = $request->input('cancelled');
        $gym = $request->input('gym');
        $type = $request->input('type');

        $date_beg = ($date_beg == "") ? null: $date_beg;
        $date_end = ($date_end == "") ? null: $date_end;
        $coach = ($coach == "") ? null: $coach;
        $customer = ($customer == "") ? null: $customer;
        $cancelled = ($cancelled == null) ? 2 : $cancelled;
        $gym = ($gym == null) ? 0: $gym;
        $type = ($type == null) ? 0: $type;

        //тренировки на которые был записан клиент
        if($customer != null){
            $customer = Customer::all()->where('passport', $customer)->first();
            $workouts = SignUpGroupWorkout::all()->where('customer_id', $customer->id)->pluck('group_workout_id');
            $workouts = GroupWorkout::all()->whereIn('id', $workouts);
        } else {
            $workouts = GroupWorkout::all();
        }

        $workouts = match (true) {
            $date_beg == null && $date_end == null => $workouts,
            $date_beg != null && $date_end == null => $workouts->where('event', $date_beg),
            ($date_beg != null && $date_end != null) && ($date_beg < $date_end) => $workouts->whereBetween('event', [$date_beg, $date_end]),
            ($date_beg != null && $date_end != null) && ($date_beg > $date_end) => $workouts->whereBetween('event', [$date_end, $date_beg]),
            default => $workouts->where('event', $date_end),
        };

        if($coach != null){
            $coach = Coach::all()->where('passport', $coach)->first();
            $schedules = Schedule::all()->where('coach_id',$coach->id)->pluck('id');
            $workouts = $workouts->whereIn('schedule_id', $schedules);
        }

        $workouts = ($cancelled != '2') ? $workouts->where('cancelled', $cancelled) : $workouts;
        $workouts = ($gym != '0') ? $workouts->where('schedule.gym_id', $gym) : $workouts;
        $workouts = ($type != '0') ? $workouts->where('schedule.workout_type_id', $type) :$workouts;

        if($workouts->count() != 0) {
            $workouts = $workouts->pluck('id');
            $workouts = GroupWorkout::with('schedule.gym', 'schedule.workout_type', 'schedule.coach', 'schedule.day')
                ->whereIn('id', $workouts)
                ->orderByDesc('event')
                ->paginate(14);
        }

        return response()->json($workouts);
    }

    // показать тренеровки по расписанию
    public function selectWorkoutsBySchedule(Request $request): JsonResponse {

        $id = $request->input('id');

        return response()->json(GroupWorkout::with('schedule.gym', 'schedule.workout_type', 'schedule.coach', 'schedule.day')
        ->where('schedule_id', $id)
        ->orderByDesc('event')
        ->paginate(14));
    }
}
