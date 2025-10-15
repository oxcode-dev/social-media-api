<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class LoginController extends BaseController
{
     /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) //: JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'sometimes|boolean',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(!User::where('email', $request->email)->where('role', 'CUSTOMER')->exists()){ 
            return $this->sendError('These credentials do not match our records.', ['email'=> 'Invalid Credentials.']);  
        } 

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['first_name'] =  $user->first_name;
            $success['last_name'] =  $user->last_name;
            $success['email'] =  $user->email;
            $success['phone'] =  $user->phone;
            $success['avatar'] =  $user->avatar;
            $success['name'] =  $user->name;
            $success['id'] =  $user->id;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        
        return $this->sendError('These credentials do not match our records.', ['email'=>'Invalid Credentials.']);
    }

    public function logout(Request $request)
    {
        if($request->user() !== null) {
            $request->user()->tokens()->delete();
        }

        return $this->sendResponse('success', 'Successfully logged out user...');
    }
}
