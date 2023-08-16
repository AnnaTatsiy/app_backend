<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LimitedPriceList;
use Illuminate\Http\JsonResponse;

class LimitedPriceListController extends Controller
{
    // получить все записи (вывести прайс лист на тренировки с тренерами) c без пагинации
    public function getLimitedPriceLists(): JsonResponse{
        return response()->json(LimitedPriceList::with('coach')->get());
    }

    // получить все записи (вывести прайс лист на тренировки с тренерами) c пагинацией
    public function limitedPriceLists(): JsonResponse{
        return response()->json(LimitedPriceList::with('coach')->orderBy('price')->paginate(12));
    }

    // добавить прайс лист, добавляем сразу 2 записи (на 8 и на 12 тренировок)
    // добавление происходит сразу после добавления тренера
    // стоимость абонемента выставляется автоматически, тренер сам должен в последствии
    // редактировать ее
    static public function addLimitedPriceList($coach_id): void{

        $price01 = new LimitedPriceList();
        $price01->coach_id = $coach_id;
        $price01->amount_workout = 8;
        $price01->price = 5000;

        $price02 = new LimitedPriceList();
        $price02->coach_id = $coach_id;
        $price02->amount_workout = 12;
        $price02->price = 7000;

        $price01->save();
        $price02->save();
    }

    // редактирвать прайс лист
    //TODO реализовать, можно редактировать только стоимость
}
