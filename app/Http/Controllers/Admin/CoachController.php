<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoachController extends Controller {

    // получить все записи (вывод всех тренеров)
    public function coachesAll(): JsonResponse{
        return response()->json(Coach::all());
    }

    // получить все записи (вывод всех тренеров) постранично
    public function coaches(): JsonResponse{
        return response()->json(Coach::paginate(12));
    }

    //добавление тренера
    public function addCoach(Request $request):JsonResponse{
        return response()->json($this->saveInDB(new Coach(), $request));
    }

    //редактирование тренера
    public function editCoach(Request $request):JsonResponse{
        $coach = Coach::all()->where('id', $request->input('id'))->first();

        $response = ($coach != null) ? $this->saveInDB($coach, $request) : 0;
        return response()->json($response);
    }

    //сохранение тренера в БД
    public function saveInDB(Coach $coach, Request $request): Coach{
        $coach->surname = $request->input('surname');
        $coach->name = $request->input('name');
        $coach->patronymic = $request->input('patronymic');
        $coach->passport = $request->input('passport');
        $coach->birth = $request->input('birth');
        $coach->mail = $request->input('mail');
        $coach->number = $request->input('number');
        $coach->registration = $request->input('registration');

        $coach->save();

        LimitedPriceListController::addLimitedPriceList($coach->id);

        return $coach;
    }
}
