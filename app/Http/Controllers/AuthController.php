<?php

namespace App\Http\Controllers;

use Auth;
use JWTAuth;
use Validator;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

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

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response([
                    'status' => 'error',
                    'error' => 'invalid.credentials',
                    'msg' => 'Invalid Credentials.'
                ], 401);
            }
            /*
            //Creating a Token based on a user object

            $customClaims = ['foo' => 'bar', 'baz' => 'bob'];
                JWTAuth::attempt($credentials, $customClaims);
                // or
                JWTAuth::fromUser($user, $customClaims);
            //----------------------------------------------------
            //Creating a Token based on anything you like

            $customClaims = ['foo' => 'bar', 'baz' => 'bob'];
            $payload = JWTFactory::make($customClaims);
            $token = JWTAuth::encode($payload);
            */
        }catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return $this->respondWithToken($token);

    }

    
    public function me(){
        //the same
        $userDB = User::find(Auth::user()->id);
        $userSession = $this->guard()->user();

        return response([
            'status' => 'success',
            'data' => $userSession //or userDB
        ]);
    }

    public function refresh(){
        return $this->respondWithToken($this->guard()->refresh());
    }

    public function logout()    {
        JWTAuth::invalidate();

        return response([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function guard(){
        return Auth::guard();
    }

}
