<?php

use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\GroupWorkoutController;
use App\Http\Controllers\Admin\GymController;
use App\Http\Controllers\Admin\LimitedPriceListController;
use App\Http\Controllers\Admin\LimitedSubscriptionController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SignUpGroupWorkoutController;
use App\Http\Controllers\Admin\SignUpPersonalWorkoutController;
use App\Http\Controllers\Admin\UnlimitedPriceListController;
use App\Http\Controllers\Admin\UnlimitedSubscriptionController;
use App\Http\Controllers\Admin\WorkoutTypeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (){
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Сторона Администратора:
    // получить все записи (вывод всех тренеров)
    Route::get('coaches/get-all', [CoachController::class, 'coachesAll'])->middleware('restrictRole:admin');

    // получить все записи (вывод всех тренеров) постранично
    Route::get('coaches/all', [CoachController::class, 'coaches'])->middleware('restrictRole:admin');
    // сохранить тренера в бд
    Route::post('coaches/add', [CoachController::class, 'addCoach'])->middleware('restrictRole:admin');
    // редактирование тренера в бд
    Route::post('coaches/edit', [CoachController::class, 'editCoach'])->middleware('restrictRole:admin');

    // получить все записи (вывод всех клиентов)
    Route::get('customers/get-all', [CustomerController::class, 'customersAll'])->middleware('restrictRole:admin');
    // получить все записи (вывод всех клиентов) постранично
    Route::get('customers/all', [CustomerController::class, 'customers'])->middleware('restrictRole:admin');
    // сохранить клиента в бд
    Route::post('customers/add', [CustomerController::class, 'addCustomer'])->middleware('restrictRole:admin');
    // редактирование клиента в бд
    Route::post('customers/edit', [CustomerController::class, 'editCustomer'])->middleware('restrictRole:admin');
    // поиск клиента по серии-номеру паспорта
    Route::post('customers/select-customers-by-passport', [CustomerController::class, 'getCustomersByPassport'])->middleware('restrictRole:admin');

    // вывод всех спортзалов
    Route::get('gyms/get-all', [GymController::class, 'getAllGyms'])->middleware('restrictRole:admin');

    Route::get('workout-types/get-all', [WorkoutTypeController::class, 'getAllWorkoutTypes'])->middleware('restrictRole:admin');

    // получить все записи (вывод всех групповых тренировок)
    Route::get('group-workouts/get-all', [GroupWorkoutController::class, 'getGroupWorkouts'])->middleware('restrictRole:admin');
    // получить все записи (вывод всех групповых тренировок) постранично
    Route::get('group-workouts/all', [GroupWorkoutController::class, 'groupWorkouts'])->middleware('restrictRole:admin');
    //получить всю информацию о групповой тренировки по id
    Route::get('group-workouts/select-by-id/{id}', [GroupWorkoutController::class, 'groupWorkoutById'])->middleware('restrictRole:admin');
    //редактирование тренировки - возможна только отмена
    Route::post('group-workouts/group-workout-edit', [GroupWorkoutController::class, 'groupWorkoutEdit'])->middleware('restrictRole:admin');
    // получить все тренировки пройденные через фильтр
    Route::get('group-workouts/filtered/', [GroupWorkoutController::class, 'groupWorkoutsFiltered'])->middleware('restrictRole:admin');

    // получить все записи (вывести прайс лист на тренировки с тренерами) постранично
    Route::get('limited-price-lists/all', [LimitedPriceListController::class, 'limitedPriceLists'])->middleware('restrictRole:admin');
    // получить все записи (вывести прайс лист на тренировки с тренерами)
    Route::get('limited-price-lists/get-all', [LimitedPriceListController::class, 'getLimitedPriceLists'])->middleware('restrictRole:admin');

    // получить все записи (вывести все подписки на тренировки с тренерами)
    Route::get('limited-subscriptions/get-all', [LimitedSubscriptionController::class, 'getLimitedSubscriptions'])->middleware('restrictRole:admin');
    // получить все записи (вывести все подписки на тренировки с тренерами) постранично
    Route::get('limited-subscriptions/all', [LimitedSubscriptionController::class, 'limitedSubscriptions'])->middleware('restrictRole:admin');
    //добавить подписку на групповые тренировки
    Route::post('limited-subscriptions/add', [LimitedSubscriptionController::class, 'addLimitedSubscription'])->middleware('restrictRole:admin');

    // вывести расписание групповых тренировок
    Route::get('schedules/all', [ScheduleController::class, 'schedulesGetAll'])->middleware('restrictRole:admin');

    // получить все записи на групповые тренировки
    Route::get('sign-up-group-workouts/all', [SignUpGroupWorkoutController::class, 'signUpGroupWorkouts'])->middleware('restrictRole:admin');
    //получить всю информацию о групповой тренировки по id
    Route::get('sign-up-group-workouts/select-by-workout-id/{id}', [SignUpGroupWorkoutController::class, 'selectSignUpGroupWorkoutsByWorkoutId'])->middleware('restrictRole:admin');

    // получить все записи на персональные тренировки
    Route::get('sign-up-personal-workouts/all', [SignUpPersonalWorkoutController::class, 'signUpPersonalWorkouts'])->middleware('restrictRole:admin');
    //получить все тренировки пройденные через фильтр
    Route::get('sign-up-personal-workouts/filtered/', [SignUpPersonalWorkoutController::class, 'signUpPersonalWorkoutsFiltered'])->middleware('restrictRole:admin');

    // получить все записи (вывести прайс лист на безлимит абонементы)
    Route::get('unlimited-price-lists/all', [UnlimitedPriceListController::class, 'unlimitedPriceLists'])->middleware('restrictRole:admin');

    // получить все записи (вывести все подписки на безлимит абонемент)
    Route::get('unlimited-subscriptions/get-all', [UnlimitedSubscriptionController::class, 'getAllUnlimitedSubscriptions'])->middleware('restrictRole:admin');
    // получить все записи (вывести все подписки на безлимит абонемент) постранично
    Route::get('unlimited-subscriptions/all', [UnlimitedSubscriptionController::class, 'unlimitedSubscriptions'])->middleware('restrictRole:admin');
    // Сторона Администратора: безлимит абонементы данного клиента.
    Route::post('unlimited-subscriptions/select-unlimited-subscriptions-by-customer', [UnlimitedSubscriptionController::class, 'selectUnlimitedSubscriptionsByCustomer'])->middleware('restrictRole:admin');
    // добавить абонемент
    Route::post('unlimited-subscriptions/add', [UnlimitedSubscriptionController::class, 'addUnlimitedSubscription'])->middleware('restrictRole:admin');

    // Сторона Администратора: купленные тренировки данного клиента.
    Route::post('limited-subscriptions/select-limited-subscriptions-by-customer', [LimitedSubscriptionController::class, 'selectLimitedSubscriptionsByCustomer'])->middleware('restrictRole:admin');
});





