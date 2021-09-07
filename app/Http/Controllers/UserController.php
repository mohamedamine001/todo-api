<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * Register User
     *
     * @param Request $request
     * @return void
     */

    public function register(Request $request){

        $validator = Validator::make($request->all(),[
            "first_name" => "required",
            "last_name"  => "required",
            "email"      => "required|email|unique:users,email",
            "password"   => "required|min:5"
         ]);
         if($validator->fails()){
             return $this->validationErrors($validator->errors());
         }

         $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password)
         ]);

        return response()->json(["status"=>"success","error"=>false,"message"=>"User registered Successfully!"],201);
    }
    /**
     * User Login
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            "email" => "required|email",
            "password" => "required|min:5"
        ]);

        if($validator->fails()){
            return $this->validationErrors($validator->errors());
        }
        try{
            if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
                $user  = Auth::user();
                $token = $user->createToken('token')->accessToken;
                return response()->json([
                    "status"  =>"success",
                    "error"   => false,
                    "message" => "Logged in successfully!",
                    "token" => $token,
                ]); 
            }
            return response()->json(["status"=>"failed","message"=>"Login failed"],404);
        }catch(Exception $e){
            return response()->json(["status"=>"failed","message"=>$e->getMessage()],404);
        }
    }

    /**
     * Logged User Data Using Auth Token
     *
     * @return void
     */
    public function user(){
        try{
            $user = Auth::user();
            return response()->json(["status"=>"success","error"=>false,"data"=>$user],200);
        }catch(NotFoundHttpException  $e){
            return resposne()->json(["status"=>"failed","error"=>$e],401);
        }
    }



    /**
    * Logout Auth User
    *
    * @param Request $request
    * @return void
    */
    public function logout(){
        if(Auth::check()){
            Auth::user()->token()->revoke();
            return response()->json(["status"=>"success","error"=>false,"message"=>"Logged out successfully!"],200);
        }
        return response()->json(["status"=>"failed","error"=>true,"message"=>"Already logged-out !"],403);
    
    }
    
}
