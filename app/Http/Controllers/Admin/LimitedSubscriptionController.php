<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Customer;
use App\Models\LimitedPriceList;
use App\Models\LimitedSubscription;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LimitedSubscriptionController extends Controller
{
    // получить все записи (вывести все подписки на тренировки с тренерами)
    public function getLimitedSubscriptions(): JsonResponse{
        return response()->json(LimitedSubscription::with('customer', 'limited_price_list.coach')->orderByDesc('open')->get());
    }

    // получить все записи (вывести все подписки на тренировки с тренерами) постранично
    public function limitedSubscriptions(): JsonResponse{
        return response()->json(LimitedSubscription::with('customer', 'limited_price_list.coach')->orderByDesc('open')->paginate(12));
    }

    //Сторона Администратора: купленные тренировки данного клиента.
    public function selectLimitedSubscriptionsByCustomer(Request $request): JsonResponse{
        $id = $request->input('customer');
        return response()->json(LimitedSubscription::with( 'limited_price_list', 'coach')->where('customer_id', '=', $id)->get());
    }

    //добавить подписку на групповые тренировки
    public static function addLimitedSubscription(Request $request): JsonResponse{

        $limited_price_list = LimitedPriceList::all()->where('coach_id', Coach::all()->where('passport',$request->input('coach'))->first()->id)
            ->where('amount_workout', $request->input('amount_workout'))->first();

        $sub = new LimitedSubscription();
        $sub->customer_id = Customer::all()->where('passport',$request->input('customer'))->first()->id;
        $sub->limited_price_list_id=$limited_price_list->id;
        $sub->open = date_format(new DateTime(), 'Y-m-d' );

        $sub->save();

        return response()->json(LimitedSubscription::with('customer', 'limited_price_list.coach')->where('id',$sub->id)->first());
    }
}
