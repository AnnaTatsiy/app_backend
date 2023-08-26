<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\user\Password;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        // получаю поля из запроса
        $surname =  $request->input('surname');
        $name = $request->input('name');
        $patronymic = $request->input('patronymic');
        $email = $request->input('mail');
        $passport = $request->input('passport');
        $birth = $request->input('birth');
        $number = $request->input('number');
        $registration = $request->input('registration');

        // генерирую пароль
        $password = Str::random(8);

        //отсылаю пароль на почту
        Mail::to($email)->send(new Password($surname, $name ,$patronymic, $password, $birth, $email, $passport ,$number, $registration, "добро пожаловать в нашу команду!"));

        // создаю личный кабинет
        $user = User::create(
            [
                'name' => $name . " " . $patronymic,
                'email' => $email,
                'password' => bcrypt($password),
                'role' => 'coach',
            ]
        );

        //сохраняю в БД
        $coach = new Coach();

        $coach->surname = $surname;
        $coach->name = $name;
        $coach->patronymic = $patronymic;
        $coach->passport = $passport;
        $coach->birth = $birth;
        $coach->mail = $email;
        $coach->number = $number;
        $coach->user_id = $user->id;
        $coach->registration = $registration;

        $coach->save();

        // создаю прйс лист на индивидуальные тренировки
        LimitedPriceListController::addLimitedPriceList($coach->id);

        return response()->json($coach);
    }

    //редактирование тренера
    public function editCoach(Request $request):JsonResponse{
        $coach = Coach::all()->where('id', $request->input('id'))->first();

        $coach->surname = $request->input('surname');
        $coach->name =  $request->input('name');
        $coach->patronymic = $request->input('patronymic');
        $coach->passport = $request->input('passport');
        $coach->birth = $request->input('birth');
        $coach->mail = $request->input('mail');
        $coach->number =$request->input('number');
        $coach->registration = $request->input('registration');

        $coach->save();

        return response()->json($coach);
    }
}
