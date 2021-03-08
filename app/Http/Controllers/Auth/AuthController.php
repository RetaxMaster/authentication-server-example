<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    
    public function register(RegisterRequest $request) {

        $users = User::where("email", "=", $request->email)->count();

        if ($users <= 0) {

            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password)
            ]);

            return response()->json(array(
                "email" => $user->email
            ), 201);

        }
        else { 

            return response()->json(array(
                "status" => false,
                "message" => "El usuario ya existe"
            ), 401);

        }

        
    }

}

/*

Register User
curl -X POST http://127.0.0.1:8000/api/register \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-d '{ 
    "email": "user@test.com", 
    "password": "123456",
    "password_confirmation": "123456"
}'



Request Password Grant Token
curl -X POST http://127.0.0.1:8000/oauth/token \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-d '{ 
    "grant_type": "password", 
    "client_id": "92d16637-6c65-42c4-9ba2-b940d8dc3d0b",
    "client_secret": "Sx20PJJkmED46X2HzvhoLPHeXnoE7sNp6hNqTdBw",
    "username": "user@test.com",
    "password": "123456",
    "scope": ""
}'

*/