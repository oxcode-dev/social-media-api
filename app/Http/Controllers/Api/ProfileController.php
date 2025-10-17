<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseController as BaseController;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use Illuminate\Validation\Rules\Password;

class ProfileController extends BaseController
{
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->sendError('Validation Error.', ['status' => 'failed', 'message' => 'user not found'], 419);       
        }

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [ 'required', 'string','lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:255'],
        ]);
   
        if($validator->fails()){
            return $this->sendError('Error Occurred.', $validator->errors());       
        }

        $input = $request->all();

        $request->user()->fill($input);
        $request->user()->save();

        return $this->sendResponse($user, 'User Profile Updated Successfully.');
        // return $this->sendResponse(new UserResource($user), 'User Profile Updated Successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->sendError('Validation Error.', ['status' => 'failed', 'message' => 'user not found'], 419);       
        }
        
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults()],
            'confirm_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Error Occurred.', $validator->errors());       
        }

        
        $user->update([
            'password' => bcrypt($request->get('password')),
        ]);

        return $this->sendResponse(['Password Changed Successfully'], 'Password Changed Successfully.');
    }

    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'current_password'],
        ]);

        if($validator->fails()){
            return $this->sendError('Error Occurred.', $validator->errors());       
        }

        if($request->user() !== null) {
            $user = $request->user();

            $user->addresses()->delete();
            $user->orders()->delete();
            $user->wishlists()->delete();

            $user->delete();

            $request->user()->tokens()->delete();

            return $this->sendResponse(['Account Deleted Successfully'], 'Account Deleted Successfully.');
        }
    }
}
