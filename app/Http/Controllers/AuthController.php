<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|min:10|max:100',
            'role' => 'required|string|in:admin,user',
            'email' => 'required|email|max:50|unique:users,email',
            'password' => 'required|string|min:10|confirmed'
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()],422);
        }

        User::create([
            'name'=>$request->get('name'),
            'role'=>$request->get('role'),
            'email'=>$request->get('email'),
            'password'=>bcrypt($request->get('pasword')),
        ]);
        return response()->json(['message'=>'User Created Succesfully'],201);
    }

    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            'email' => 'required|string|email|max:50',
            'password' => 'required|string|min:10'
        ]);

        if ($validator->fails()){
            return response()->json(['error'=>$validator->errors()],422);
        }

        $credentials = $request->only(['email','password']);
        
        try {
            if(!$token=JWTAuth::attempt($credentials)){
                return response()->json(['error'=>'Invalid Credentials'], 401);
            }
            return response()->json(['token'=>$token],200);

        } catch (JWTException $e) {
            return response()->json(['error'=>'Could Not Create Token'], 500);

        }
    }

    public function getUser(){
        $user = JWTAuth::user();
        return response()->json($user, 200);
    }

    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Logged Out succesfully!'],200); 
    }
}
