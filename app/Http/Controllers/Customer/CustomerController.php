<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\UnlimitedSubscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //получает информацию о текущем абонементе (безлимит)
    public function aboutSubscription(): JsonResponse
    {
        $user = User::with('customer')->where('email', auth()->user()->email)->first();

        $customer = Customer::with('user')->where('user_id', $user->id)->first();

        $subscription = UnlimitedSubscription::with( 'unlimited_price_list.subscription_type')
            ->where('customer_id', $customer->id)
            ->orderByDesc('open')
            ->first();

        return response()->json($subscription);
    }
}
