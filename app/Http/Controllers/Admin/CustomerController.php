<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\user\Password;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomerController extends Controller
{

    // получить все записи (вывод всех клиентов)
    public function customersAll(): JsonResponse{
        return response()->json(Customer::all());
    }

    // получить все записи (вывод всех клиентов) постранично
    public function customers(): JsonResponse{
        return response()->json(Customer::paginate(12));
    }

    // поиск клиента по серии-номеру паспорта
    public function getCustomersByPassport(Request $request): JsonResponse{
        return response()->json(Customer::all()->where('passport', $request->input('passport')));
    }

    //добавление клиента
    public function addCustomer(Request $request):JsonResponse{
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
        Mail::to($email)->send(new Password($surname, $name ,$patronymic, $password, $birth, $email, $passport ,$number, $registration, "спасибо, что выбрали нас!"));

        // создаю личный кабинет
        $user = User::create(
            [
                'name' => $name . " " . $patronymic,
                'email' => $email,
                'password' => bcrypt($password),
                'role' => 'customer',
            ]
        );

        $customer = new Customer();

        $customer->surname =  $surname;
        $customer->name = $name;
        $customer->patronymic = $patronymic;
        $customer->passport = $passport;
        $customer->birth = $birth;
        $customer->mail = $email;
        $customer->number = $number;
        $customer->user_id = $user->id;
        $customer->registration = $registration;

        $customer->save();

        return response()->json($customer);
    }

    //редактирование клиента
    public function editCustomer(Request $request):JsonResponse{
        $customer = Customer::all()->where('id', $request->input('id'))->first();
        $user = User::all()->where('id', $customer->user_id)->first();

        $customer->surname = $request->input('surname');
        $customer->name =  $request->input('name');
        $customer->patronymic = $request->input('patronymic');
        $customer->passport = $request->input('passport');
        $customer->birth = $request->input('birth');
        $customer->mail = $request->input('mail');
        $user->email = $request->input('mail');
        $customer->number =$request->input('number');
        $customer->registration = $request->input('registration');

        $customer->save();
        $user->save();

        return response()->json($customer);
    }

    //проверка данных на уникальность

    //проверка паспорта
    public function checkingUniquePassport($value): JsonResponse
    {
        return response()->json([
                'result' => count(Customer::all()->where('passport', $value))
            ]);
    }

    //проверка номера телефона
    public function checkingUniqueNumber($value): JsonResponse
    {
        return response()->json([
            'result' => count(Customer::all()->where('number', $value))
        ]);
    }

    //проверка email
    public function checkingUniqueMail($value): JsonResponse
    {
        return response()->json([
            'result' => count(Customer::all()->where('mail', $value))
        ]);
    }
}
