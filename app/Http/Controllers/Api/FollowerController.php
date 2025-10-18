<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;

class FollowerController extends BaseController
{
    public function store(Request $request, $id)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return $this->sendError('Validation Error.', ['status' => 'failed', 'message' => 'user not found'], 419);       
        }

        $user = User::find($id);
        if(!$user) {
            return $this->sendError('Error Occurred.', ['status' => 'failed', 'message' => 'User not found'], 419);       
        }

        if(!Follower::where('following_id', $id)->where('follower_id', $authUser->id)->first()) {
            $following = new Follower();
            $following->following_id = $id;
            $following->follower_id = $authUser->id;

            $following->save();
            
            return $this->sendResponse(['User Followed Successfully'], 'User Followed Successfully.');
        }

        return $this->sendResponse([], 'You are already following this user!!!.');

    }

    public function destroy(Request $request, $id)
    {
        return [$request->user(), $id];
        $authUser = $request->user();

        if (!$authUser) {
            return $this->sendError('Validation Error.', ['status' => 'failed', 'message' => 'user not found'], 419);       
        }

        $user = User::find($id);
        if(!$user) {
            return $this->sendError('Error Occurred.', ['status' => 'failed', 'message' => 'User not found'], 419);       
        }

        if($following = Follower::where('following_id', $id)->where('follower_id', $authUser->id)->first()) {
            $following->delete();
            
            return $this->sendResponse(['User Unfollowed Successfully'], 'User Unfollowed Successfully.');
        }

        return $this->sendResponse([], 'You are not following this user!!!.');
    }

    public function getFollowers(Request $request, $id)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return $this->sendError('Validation Error.', ['status' => 'failed', 'message' => 'user not found'], 419);       
        }

        $user = User::with(['followers'])->whereId($id)->firstOrFail();

        return $user;
    }

    public function getFollowings(Request $request, $id)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return $this->sendError('Validation Error.', ['status' => 'failed', 'message' => 'user not found'], 419);       
        }

        $user = User::with(['followings'])->whereId($id)->firstOrFail();

        return $user;
    }
}
