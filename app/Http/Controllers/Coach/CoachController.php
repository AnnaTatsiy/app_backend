<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\LimitedPriceList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoachController extends Controller
{
    //получить тренера из авторизированного пользователя
    private function getCoach()
    {
        $user = User::with('coach')->where('email', auth()->user()->email)->first();
        return Coach::with('user')->where('user_id', $user->id)->first();
    }

    //получить тренера из авторизированного пользователя JSON
    public function getCoachJSON(): JsonResponse{
        return response()->json($this->getCoach());
    }

    //может изменить цену на абонемент (упрощение модели)
    public function editLimitedPrice(Request $request): JsonResponse
    {
        $response = [];

        $lo = $request->input('lo');
        $hi = $request->input('hi');

        $validator = Validator::make($request->all(),
            [
                'lo' => "bail|required|numeric",
                'hi' => "bail|required|numeric"
            ],
            [
                'lo.required' => "Стоимость не была указана",
                'hi.required' => "Стоимость не была указана",
                'lo.numeric' => "Введите обе стоимости",
                'hi.numeric' => "Введите обе стоимости"
            ]
        );

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "message" => "Validation error", "errors" => $validator->errors()->messages()[$validator->errors()->keys()[0]][0]]);
        }

        $validator = Validator::make($request->all(),
            [
                'lo' => "bail|required|numeric|min:1000|max:{$hi}",
                'hi' => "bail|required|numeric|min:{$lo}|max:20000"
            ],
            [
                'lo.min' => "Минимальная стоимость абонемента на 8 посещений 1000",
                'hi.min' => "Минимальная стоимость абонемента на 12 посещений ${lo}",
                'lo.max' => "Максимальная стоимость абонемента на 8 посещений ${hi}",
                'hi.max' => "Максимальная стоимость абонемента на 12 посещений 20000"
            ]
        );

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "message" => "Validation error", "errors" => $validator->errors()->messages()[$validator->errors()->keys()[0]][0]]);
        }

        if ($request->has('lo') && $request->has('hi')) {

            $coach = $this->getCoach();

            //находим прайс абонемента на 8 и 12 посещений
            $price_lo = LimitedPriceList::all()->where('coach_id', $coach->id)->where('amount_workout', 8)->first();
            $price_hi = LimitedPriceList::all()->where('coach_id', $coach->id)->where('amount_workout', 12)->first();

            //изменить цену абенемента на 8 посещений
            $price_lo->price = $lo;
            $price_lo->save();

            //изменить цену абенемента на 12 посещений
            $price_hi->price = $hi;
            $price_hi->save();

            $response["status"] = "success";
            $response["message"] = "Изменения успешно внесены!";

        } else {

            $response["status"] = "failed";
            $response["message"] = "Failed!";
        }
        return response()->json($response);

    }

    //получить признак доступна ли продажа абонементов
    public function getSale(): JsonResponse
    {
        $coach = $this->getCoach();

        return response()->json(
            ["sale" => (bool) $coach->sale]
        );
    }

    // тренер может запретить продажу абонементов
    public function changeSale(): JsonResponse
    {
        $coach = $this->getCoach();
        $sale = (bool) $coach->sale;

        $coach->sale = !$sale;
        $coach->save();

        return response()->json(
            ["sale" => (bool) $coach->sale]
        );
    }
}
