<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnlimitedPriceList;
use Illuminate\Http\JsonResponse;

class UnlimitedPriceListController extends Controller
{
    // получить все записи (вывести прайс лист на безлимит абонементы)
    public function unlimitedPriceLists(): JsonResponse{
        return response()->json(UnlimitedPriceList::with('subscription_type')->get());
    }
}
