<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\UnlimitedPriceList;
use App\Models\UnlimitedSubscription;
use Codedge\Fpdf\Fpdf\Fpdf;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnlimitedSubscriptionController extends Controller
{
    // получить все записи (вывести все подписки на безлимит абонемент)
    public function getAllUnlimitedSubscriptions(): JsonResponse{
        return response()->json(UnlimitedSubscription::with( 'unlimited_price_list.subscription_type', 'customer')->orderByDesc('open')->get());
    }

    // получить все записи (вывести все подписки на безлимит абонемент) постранично
    public function unlimitedSubscriptions(): JsonResponse{
        return response()->json(UnlimitedSubscription::with('unlimited_price_list.subscription_type', 'customer')->orderByDesc('open')->paginate(12));
    }

    //Сторона Администратора: безлимит абонементы данного клиента.
    public function selectUnlimitedSubscriptionsByCustomer(Request $request): JsonResponse{
        $id = $request->input('customer');
        return response()->json(UnlimitedSubscription::with( 'unlimited_price_list', 'subscription_type')->where('customer_id', '=', $id)->get());
    }

    // добавить абонемент
    public function addUnlimitedSubscription(Request $request): JsonResponse //: Response
    {
        $subscription_type = $request->input('subscription_type');
        $validity_period = $request->input('validity_period');

        //Признак: оформлять вместе с абонементом подписку на групповые тренировки ?
        $isAddLimitedSubscription = $request->input('is_add_lim');

        $unlimited_price_list = UnlimitedPriceList::all()->where('subscription_type_id', $subscription_type)
            ->where('validity_period', $validity_period)->first();

        $customer_passport = $request->input('customer');

        $sub = new UnlimitedSubscription();
        $sub->customer_id = Customer::all()->where('passport',$customer_passport)->first()->id;
        $sub->unlimited_price_list_id = $unlimited_price_list->id;
        $sub->open = date_format(new DateTime(), 'Y-m-d' );

        $sub->save();

        if($isAddLimitedSubscription){
            LimitedSubscriptionController::addLimitedSubscription($request);
        }

        //TODO добавить создание договора о покупки абонемента
        return response()->json(UnlimitedSubscription::with( 'unlimited_price_list.subscription_type', 'customer')->where('id',$sub->id)->first());

    }

    //генерация договора о оказании услуг  //TODO в разработке...
    public function generate(Request $request)
    {

        $customer_passport = $request->input('customer_passport');

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->addFont('Times-New-Roman','','times new roman.php');
        $pdf->SetFont('Helvetica','',14);
        $pdf->Cell(40,10,'Привет');
        $pdf->Output();
        exit;
        /*
        $pdf = Pdf::loadView('export-contract', [

        ]);

        $pdf->setPaper('A4');

        return $pdf->download("договор_клиента_№_паспорта=$customer_passport.pdf"); */
    }
}
