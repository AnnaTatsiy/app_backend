<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\Request;

class CoachController extends Controller
{
    //получить тренера из авторизированного пользователя
    private function getCoach()
    {
        $user = User::with('coach')->where('email', auth()->user()->email)->first();
        return Coach::with('user')->where('user_id', $user->id)->first();
    }

    //может изменить цену на абонемент (упрощение модели)
    public function editLimitedPrice(Request $request){
        $coach = $this->getCoach();

        //изменить цену абенемента на 8 посещений
        $lo = $request->input('lo');


        //изменить цену абенемента на 12 посещений
        $lo = $request->input('hi');
    }
}
