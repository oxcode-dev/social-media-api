<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;

use Illuminate\Http\Request;

class FollowerController extends BaseController
{
    public function store(Request $request, $id)
    {
        return [$request->all(), $id];
    }
}
