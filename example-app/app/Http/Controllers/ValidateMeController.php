<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostValidateMeRequest;

class ValidateMeController extends Controller
{
    public function post(PostValidateMeRequest $request)
    {
        return response("OK");
    }

    public function get(PostValidateMeRequest $request)
    {
        return view('validate-me');
    }
}
