<?php

namespace Database\Seeders;

use App\Http\Helpers\Utils;
use App\Models\coach;
use App\Models\Customer;
use App\Models\GroupWorkout;
use App\Models\Schedule;
use App\Models\UnlimitedPriceList;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $faker = app(Generator::class);

        // кол-во типов абонементов
        $count_subscription_types = count(Utils::$subscription_types);

        // заполнение таблицы тренеры и клиенты
        Coach::factory(Utils::$count_coaches)->create();
        // заполнение таблицы клиенты
        Customer::factory(Utils::$count_customers)->create();

        // заполнение таблицы дни недели (добавление и изменение данных не будет)
        DB::table('days')->insert([
            ['title'=>"ПН"],
            ['title'=>"ВТ"],
            ['title'=>"СР"],
            ['title'=>"ЧТ"],
            ['title'=>"ПТ"],
            ['title'=>"СБ"],
            ['title'=>"ВС"]
        ]);

        // заполнение таблицы типы тренеровок
        DB::table('workout_types')->insert(Utils::$workout_types);

        // заполнение таблицы типы безлимит абонементов
        DB::table('subscription_types')->insert(Utils::$subscription_types);

        //в цикле генерируются спортзалы
        for ($i = 1; $i <= Utils::$count_gyms; $i++) {
            $arr_gyms[] = ['title'=>'Спортзал № ' .$i, 'capacity' => $faker->numberBetween(10, 30)];
        }
        // заполнение таблицы спортзалы
        DB::table('gyms')->insert($arr_gyms);

        // генерируется прайс безлимит абонементов
        for ($i = 1; $i <= $count_subscription_types; $i++) {
            $arr_un_price_list[] = ['subscription_type_id'=>$i, 'validity_period'=>1, 'price'=>$i*1500];
            $arr_un_price_list[] =  ['subscription_type_id'=>$i, 'validity_period'=>3, 'price'=>$i*1800];
            $arr_un_price_list[] = ['subscription_type_id'=>$i, 'validity_period'=>6, 'price'=>$i*2000];
            $arr_un_price_list[] =  ['subscription_type_id'=>$i, 'validity_period'=>12, 'price'=>$i*2300];
        }
        // заполнение таблицы прайс безлимит абонементов
        DB::table('unlimited_price_lists')->insert($arr_un_price_list);

        // генерируется прайс абонементов (покупка тренировок у тренера)
        for ($i = 1; $i <= Utils::$count_coaches; $i++) {
            $arr_lim_price_list[] = ['coach_id' => $i, 'amount_workout' => 8, 'price' => 800 * $i];
            $arr_lim_price_list[] =  ['coach_id' => $i, 'amount_workout' => 12, 'price' => 1000 * $i];
        }

        // заполнение таблицы прайс лимит абонементов (покупка тренировок у тренера)
        DB::table('limited_price_lists')->insert($arr_lim_price_list);

        // генерация расписания
        Schedule::factory(85)->create();
        // заполнение таблицы групповые тренировки
        GroupWorkout::factory(4200)->create();

        $arr_sing_personal = array();
        // цикл по клиентам. Для каждого клиента создаем соотвествующие записи: покупка абонементов, регистрация на
        // персональные и групповые тренировки - имитация годовалой работы спорт-клуба
        for ($i = 1; $i <= Utils::$count_customers; $i++) {

            // генерирую дату открытия абонемента
            $date = Utils::randomDate((date('Y')-1).'-'.date('m').'-1',date('Y-m-d'));
            // случайно выбираю тип безлимит абонемента из прайс листа
            $unlimited_price_list_id = $faker->numberBetween(1, 4*$count_subscription_types);

            // подписываю клиента на выбранный абонемент
            $arr_un_subscriptions[] = ['customer_id'=>$i, 'unlimited_price_list_id' => $unlimited_price_list_id, 'open'=>$date];

            // каждый второй клиент купил персональные тренировки
            if($i % 2 != 0) {
                // подписываю клиента на лимит абонемент
                $arr_lim_subscriptions[] = ['customer_id' => $i, 'limited_price_list_id' => $faker->numberBetween(1, 2 * Utils::$count_coaches), 'open' => $date];

                //регистрирует клинта на тренировки с тренером
                Utils::singUpPersonalWorkout($arr_sing_personal,$date,$faker,$i);
            } else{  // если клиент не купил персональные тренировки -, то записываю на групповые

                // кол-во тренировок на которые клиент будет записан
                $n = rand(6,8);

                //поиск типа безлимит абонемента, который купил клиент
                $unlimited_price_list = UnlimitedPriceList::with('subscription_type')
                    ->where('id', '=', $unlimited_price_list_id)
                    ->get()[0];

                //проверка включает ли абонемент услугу групповые тренировки (только первый не включает)
                if($unlimited_price_list->subscription_type_id != 1){

                    //находим макс дату тренировки на которую можем записать по сроку действия абонемента
                    $max = Utils::incMonths($date,$unlimited_price_list->validity_period);

                    //находим подходящие тренировки
                    $group_workout = GroupWorkout::all()->whereBetween('event', [$date, $max]);

                    //записываем
                    for ($k = 1; $k <= $n; $k++){
                        $arr_sing_group[] = ['customer_id' => $i, 'group_workout_id' => $faker->randomElements($group_workout)[0]->id];
                    }
                }
            }
        }

        // заполнение таблицы подписки на безлимит абонементы
        DB::table('unlimited_subscriptions')->insert($arr_un_subscriptions);
        // заполнение таблицы подписки на лимит абонементы (покупка тренировок у тренера)
        DB::table('limited_subscriptions')->insert($arr_lim_subscriptions);
        // заполнение таблицы регистрации на групповые тренировки
        DB::table('sign_up_group_workouts')->insert($arr_sing_group);
        // заполнение таблицы регистрации на персональные тренировки
        DB::table('sign_up_personal_workouts')->insert($arr_sing_personal);
    }
}
