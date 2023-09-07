<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Utils;
use App\Http\Requests\ImageRequest;
use App\Models\Customer;
use App\Models\GroupWorkout;
use App\Models\Image;
use App\Models\LimitedSubscription;
use App\Models\SignUpGroupWorkout;
use App\Models\UnlimitedSubscription;
use App\Models\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    // получить клиента из авторизированного пользователя
    private function getCustomer()
    {
        $user = User::with('customer')->where('email', auth()->user()->email)->first();
        return Customer::with('user')->where('user_id', $user->id)->first();
    }

    //получает информацию о текущем абонементе (безлимит)
    public function aboutSubscription(): JsonResponse
    {
        $customer = $this->getCustomer();

        $subscription = UnlimitedSubscription::with('unlimited_price_list.subscription_type')
            ->where('customer_id', $customer->id)
            ->orderByDesc('open')
            ->first();

        return response()->json($subscription);
    }

    //получает информацию о текущем абонементе (тренировки с тренером)
    public function aboutSubscriptionWithCoach(): JsonResponse
    {
        $customer = $this->getCustomer();

        $subscription = LimitedSubscription::with('limited_price_list.coach')
            ->where('customer_id', $customer->id)
            ->orderByDesc('open')
            ->first();

        return response()->json($subscription);
    }

    //проверяем клиента
    private function checkingCustomer(): int
    {
        $customer = $this->getCustomer();

        $code = 0;

        //проверяем клиента
        //получаем абонемент клиента
        $subscription = UnlimitedSubscription::with('unlimited_price_list.subscription_type')
            ->where('customer_id', $customer->id)
            ->orderByDesc('open')
            ->first();

        //находим дату окончания дейсвия абонемента
        $date = Utils::incMonths($subscription->open, $subscription->unlimited_price_list->validity_period);

        // не может записаться на групповые тренировки если:
        //1. нет действующего абонемента
        if ($date <= date("Y-m-d")) {
            $code = 1;
        }

        //2. в тариф абонемента не входят групповые тренеровки
        if ($subscription->unlimited_price_list->subscription_type->group == 0) {
            $code = 2;
        }

        return $code;
    }

    public function checkingCustomerForGate(): bool
    {
        $code = $this->checkingCustomer();
        return ($code === 1 || $code === 2);
    }

    public function checkingCustomerShowError(): string
    {
        return match ($this->checkingCustomer()) {
            1 => "У вашего абонемента закончился срок действия!",
            2 => "В ваш тариф не входят групповые тренировки!",
            default => "ok",
        };
    }

    // получить все доступные тренировки для записи клиента
    public function getAvailableWorkouts(): JsonResponse
    {
        if (Gate::allows('checking-the-subscription')) {
            return response()->json($this->checkingCustomerShowError());
        }

        $customer = $this->getCustomer();

        $availableWorkouts_temp = array();
        $availableWorkouts = array();

        // тренировки не должны быть отменены
        $workouts = GroupWorkout::with('schedule.gym', 'schedule.workout_type', 'schedule.coach', 'schedule.day')
            ->where('cancelled', 0)->get();

        //клиент не может сделать запись на тренировку второй раз
        foreach ($workouts as $workout) {
            $count = SignUpGroupWorkout::all()
                ->where('group_workout_id', $workout->id)
                ->where('customer_id', $customer->id)
                ->count();

            if ($count == 0) {
                $availableWorkouts_temp[] = $workout;
            }
        }

        // на тренировку должно быть записано не более 20 человек
        foreach ($availableWorkouts_temp as $workout) {
            $count = SignUpGroupWorkout::all()
                ->where('group_workout_id', $workout->id)
                ->count();

            if ($count < 20) {
                $availableWorkouts[] = $workout;
            }
        }

        return response()->json($availableWorkouts);
    }

    // получить все актуальные записи клиента (на которые клиент может прийти)
    public function currentSignUp(): JsonResponse
    {
        $customer = $this->getCustomer();

        $signUpWorkouts = array();

        $workouts_id = SignUpGroupWorkout::all()
            ->where('customer_id', $customer->id)
            ->pluck('group_workout_id');

        $workouts = GroupWorkout::with('schedule.gym', 'schedule.workout_type', 'schedule.coach', 'schedule.day')
            ->whereIn('id', $workouts_id)
            ->where('event', '>=', date("Y-m-d"))->get();

        foreach ($workouts as $workout) {
            if ($workout->event == date("Y-m-d")) {
                if ($workout->schedule->time_begin > date("H:i:s")) {
                    $signUpWorkouts[] = $workout;
                }
            } else {
                $signUpWorkouts[] = $workout;
            }
        }

        return response()->json($signUpWorkouts);
    }

    // запись клиента на тренировки
    //человек может записаться максимум на 2 тренировки в день
    public function signUp(Request $request): JsonResponse
    {
        $id = $request->input('id'); // id тренировки
        $customer = $this->getCustomer();

        $workout = GroupWorkout::all()->where('id', $id)->first();

        //человек может записаться максимум на 2 тренировки в день
        $workouts_id = SignUpGroupWorkout::all()
            ->where('customer_id', $customer->id)
            ->pluck('group_workout_id');

        // находим кол-во тренировок на которые записан клиент
        $workouts = GroupWorkout::all()
            ->whereIn('id', $workouts_id)
            ->where('event', '=', $workout->event);

        foreach ($workouts as $workout) {
            $arr[] = $workout;
        }

        // если уже записан на 2 трен возвращаем их для возможности отмены
        if ($workouts->count() > 1) {
            return response()->json($arr);
        }

        $sign = new SignUpGroupWorkout();
        $sign->customer_id = $customer->id;
        $sign->group_workout_id = $id;

        $sign->save();

        return response()->json($sign);
    }

    //отмена записи на групповую тренировку
    public function deleteSignUpGroupWorkout(Request $request): JsonResponse
    {
        $id = $request->input('id'); // id тренировки
        $customer = $this->getCustomer();

        //нашли запись на тренировку которую будем удалять
        $sign = SignUpGroupWorkout::all()
            ->where('customer_id', $customer->id)
            ->where('group_workout_id', $id)->first();

        $sign->delete();

        return response()->json($sign);
    }

    // получить изображение
    public function index()
    {
        $id = auth()->user()->image_id;
        $image = Image::all()->where('id', $id)->first();
        return response()->json(["status" => "success", "data" => $image]);
    }

    //получить изображение от клиента
    public function upload(Request $request): JsonResponse
    {
        $response = [];

        $fileArray = array('image' => $request->only('image'));

        $validator = Validator::make($fileArray,
            [
                'image' => 'required',
                'image.*' => 'bail|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ],
            [
                'image' => 'Изображение не было выбрано',
                'image.max' => "Изображение слишком большое",
                'image.mimes' => "Требуется расширение файла jpg, png, jpeg, gif, svg",
                'image.image' => "Требуется изображение c расширением jpg, png, jpeg, gif, svg"
            ]
        );

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "message" => "Ошибка валидации", "errors" => $validator->errors()->messages()[$validator->errors()->keys()[0]][0]]);
        }

        if ($request->has('image')) {
            $image = $request->file('image');
            $filename = Str::random(32) . "." . $image->getClientOriginalExtension();
            $image->move('users/', $filename);

            $new_image = new Image();
            $new_image->path = $filename;
            $new_image->save();

            $user = auth()->user();
            $user->image_id = $new_image->id;
            $user->save();


            $response["status"] = "success";
            $response["message"] = "Изображение загруженно!";
        } else {
            $response["status"] = "failed";
            $response["message"] = "Ошибка сервера: изображение не загруженно.";
        }
        return response()->json($response);
    }
}
