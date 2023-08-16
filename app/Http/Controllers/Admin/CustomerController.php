<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return response()->json($this->saveInDB(new Customer(), $request));
    }

    //редактирование клиента
    public function editCustomer(Request $request):JsonResponse{
        $customer = Customer::all()->where('id', $request->input('id'))->first();

        $response = ($customer != null) ? $this->saveInDB($customer, $request) : 0;
        return response()->json($response);
    }

    //сохранение клиента в БД
    public function saveInDB(Customer $customer, Request $request): Customer{
        $customer->surname = $request->input('surname');
        $customer->name = $request->input('name');
        $customer->patronymic = $request->input('patronymic');
        $customer->passport = $request->input('passport');
        $customer->birth = $request->input('birth');
        $customer->mail = $request->input('mail');
        $customer->number = $request->input('number');
        $customer->registration = $request->input('registration');

        $customer->save();

        return $customer;
    }
}
