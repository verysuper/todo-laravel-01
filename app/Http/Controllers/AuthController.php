<?php

namespace App\Http\Controllers;

use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;

        try{
            $response = $http->post('http://test01.local:8080/oauth/token',[
                'form_params'=>[
                    'grant_type'=> 'password',
                    'client_id'=> 2,
                    'client_secret'=>'OSVYuaKxFXNOllZxzllp9cvWg0M4tqhgnTHYamXe',
                    'username'=> $request->username,
                    'password'=> $request->password,
                ]
            ]);
            return $response->getBody();
        }catch (\GuzzleHttp\Exception\BadResponseException $e){
            if($e->getCode() === 400){
                return response()->json('Invalid Request.Please enter a username or a password.', $e->getCode());
            }else if($e->getCode() === 401){
                return response()->json('You credentials are incorrect. Please try again', $e->getCode());
            }
            return response()->json('Something went wrong on the server', $e->getCode());
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key){
            $token->delete();
        });
        return response()->json('logged out successfully', 200);
    }
}
