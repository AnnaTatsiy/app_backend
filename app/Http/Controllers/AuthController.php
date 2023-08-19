<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request){
        return User::create([
            'name'=>$request->input('name'),
            'email'=> $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role')
        ]);

    }

    public function login(Request $request){

        if(!Auth::attempt($request->only('email', 'password'))){
            return response([
                'message' => "unauthorized"
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user =  Auth::user();
        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60*24);
        return response([
            'message' => $token
        ])->withCookie($cookie);
    }

    public function user(): string
    {
        return  Auth::user();
    }

    public function logout(){
        $cookie = Cookie::forget('jwt');

        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }
}
