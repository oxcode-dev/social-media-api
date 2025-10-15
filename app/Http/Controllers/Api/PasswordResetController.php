<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;

class PasswordResetController extends BaseController
{
    public function forgot(Request $request) //: \Illuminate\Http\JsonResponse
    {
        $data = $request->validate(['email' => 'required|email']);

        if (User::where('email', $data['email'])->exists()) {
            $user = User::where('email', $data['email'])->firstOrFail();
            $user->sendPasswordResetNotification();

            return $this->sendResponse(
                'email sent successfully', 
                'Forgot Password Request.'
            );
        } else {
            return $this->sendError('email does not exist.', ['error'=>'failed']);
        }
    }

    public function reset(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'otp' => 'required',
            'email' => 'required|email',
            'password' => ['required'],
            // 'password' => ['required', 'confirmed'],
        ]);

        if (
            OtpCode::where('code', $data['otp'])
                ->where('email', $data['email'])
                ->where('expires_at', '>', now())
                ->exists()
        ) {
            $user = User::where('email', $data['email'])->first();

            $user['password'] = bcrypt($data['password']);

            $user->save();

            // OtpCode::where('code', $data['otp'])->delete();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'password reset successfully',
                ],
                200
            );
        }

        return response()->json(
            [
                'status' => 'failed',
                'message' => 'this otp does not exist',
            ],
            404
        );
    }

    public function generateOtp(Request $request)//: \Illuminate\Http\JsonResponse
    {
        $data = $request->validate(['email' => 'required|email']);

        if (User::where('email', $data['email'])->exists()) {
            $user = User::where('email', $data['email'])->firstOrFail();
            $user->sendPasswordResetNotification();

            return $this->sendResponse(
                'email sent successfully', 
                'Generate OTP.'
            );
        } else {
            return $this->sendError('email does not exist.', ['error'=>'failed']);
        }
    }

}
