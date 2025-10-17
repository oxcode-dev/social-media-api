<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'phone' => 'nullable',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Error Occurred.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['email_verified_at'] = now();

        // return $input;
        $user = User::create($input);

        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        // $success['name'] =  $user->name;
        $success['username'] =  $user->username;
        $success['first_name'] =  $user->first_name;
        $success['last_name'] =  $user->last_name;
        $success['email'] =  $user->email;
        $success['phone'] =  $user->phone;
        $success['avatar'] =  $user->avatar;
        $success['name'] =  $user->name;
        $success['id'] =  $user->id;

        event(new Registered($user));
   
        return $this->sendResponse($success, 'User register successfully.');
    }
}   