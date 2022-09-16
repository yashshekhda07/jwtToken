<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
// use illuminate\Support\Facades\Hash;
use Validator;
use Hash;

class UserController extends Controller
{
    public function register(Request $request){
        $validate = Validator::make($request->all(),[
            'name'=>'required|min:2|max:11',
            'email'=>'required|email',
            'password'=>'required|confirmed',
        ]);


        if($validate->fails())
        {
            return response()->json($validate->errors(),400);
        }


        $user = User::create([  
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);


        return response()->json(['msg'=>'User Created Successsfully','user'=>$user]);
    }


    public function login(Request $request){

        $validate = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required',
        ]);


        if($validate->fails())
        {
            return response()->json($validate->errors(),400);
        }


        if(!$token = auth()->attempt($validate->validated()))
        {
            return response()->json(['msg'=>'Unauthorized']);
        }


        return $this->responseWithToken($token);
    }


    protected function responseWithToken($token){

        return response()->json([
            'access_token'=>$token,
            'type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60,

        ]);
        
    }


    public function profile()
    {

        return response()->json(auth()->user());

    }


    public function refresh()
    {

        return $this->responseWithToken(auth()->refresh());

    }


    public function logout()
    {

        auth()->logout();

        return response()->json(['msg'=>'User Logged Out Successfully']);
        
    }
}
