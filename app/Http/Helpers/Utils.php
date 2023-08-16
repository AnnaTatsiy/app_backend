<?php

namespace App\Http\Helpers;

use App\Models\Coach;
use Illuminate\Http\Request;

class Utils
{

    //кол-во генерируемых записей в таблицах
    public static int $count_customers = 100;
    public static int $count_coaches = 20;
    public static int $count_gyms = 8;

    //Отчества для фабрики клиенты и тренеры
    public static array $patronymic = ["Мирославов", "Константинов",
        "Тимофеев", "Владимиров", "Марков",
        "Ярославов", "Даниилов", "Давидов", "Ибрагимов",
        "Андреев", "Фёдоров", "Артёмов", "Александров", "Демидов",
        "Артёмов", "Давидов", "Арсентьев", "Маратов", "Даниилов",
        "Егоров", "Вадимов", "Сергеев"];

    // массив типы тренировок
    public static array $workout_types = [
        ['title'=>"Аэробика"],
        ['title'=>"Кикбоксинг"],
        ['title'=>"Тай-бо"],
        ['title'=>"Кангу Джампс"],
        ['title'=>"Body Sculpt"],
        ['title'=>"Йога"],
        ['title'=>"Body Pump"],
        ['title'=>"Круговой тренинг"],
        ['title'=>"Тренировка с петлями"],
        ['title'=>"Кроссфит"],
        ['title'=>"Пилатес"],
        ['title'=>"Зумба"],
        ['title'=>"Стретчинг"],
        ['title'=>"Бодифлекс"]
    ];

    //генератор случайной даты в диапазон
    public static function randomDate($start_date, $end_date): string {
        // Convert to timetamps
        $min = strtotime($start_date);
        $max = strtotime($end_date);

        // Generate random number using above bounds
        $val = rand($min, $max);

        // Convert back to desired date format
        return date('Y-m-d', $val);
    }

    //найти количество прошедших дней между двумя датами
    public static function subtractingDates($start, $end): int {

        $timeDiff = abs(strtotime($end) - strtotime($start));
        $numberDays = $timeDiff/86400;  // 86400 seconds in one day

        // and you might want to convert to integer
        return intval($numberDays);
    }

    //генератор случайной даты в диапазон (первый параметр дата, второй секунды)
    public static function randomDateBySeconds($start_date, $max): string {
        // Convert to timetamps
        $min = strtotime($start_date);

        $val = rand($min, $min+$max);
        return date('Y-m-d', $val);
    }

    //Прибавить к дате месяцы
    public static function incMonths($start_date, $count) : string {
        return date("Y-m-d", strtotime("+".$count." month", strtotime($start_date)));
    }

    // типы безлимит абонементов (добавление и изменение данных не будет)
    public static array $subscription_types = [
        ['title' => 'Простой', 'spa' => false, 'pool' => false, 'group' => false],
        ['title' => 'Простой+', 'spa' => false, 'pool' => false, 'group' => true],
        ['title' => 'Умный', 'spa' => false, 'pool' => true, 'group' => true],
        ['title' => 'Все включено', 'spa' => true, 'pool' => true, 'group' => true]
    ];

    //регистрирует клинта на тренировки с тренером
    public static function singUpPersonalWorkout(&$arr_sing_personal, $date, $faker, $customer_id): void {

        // записываем клиента на 8 персональных тренировок
        for ($j = 1; $j <=8; $j++){

            //клиент может купить перс тренировки на месяц, поэтому генерирую дату от начало открытия абонемента + месяц
            $arr_sing_personal[] = ['date_begin'=> Utils::randomDateBySeconds($date, 2419200),
                'time_begin'=> str_pad(rand(9,20), 2, 0, STR_PAD_LEFT).':00', // время начала тренировки
                'coach_id'=> $faker->numberBetween(1, Utils::$count_coaches), // тренер
                'customer_id' =>$customer_id // клиент
            ];
        }
    }

}
