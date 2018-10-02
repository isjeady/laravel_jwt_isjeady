<?php

namespace App\Http\Controllers;

use Auth;
use JWTAuth;
use Validator;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    //

    public function login(Request $request){
        return response(['status' => 'success']);
    }

    public function register(RegisterRequest $request){
        
        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->save();

        return response([
            'status' => 'success',
            'data' => $user
        ], 200);

    }
}
