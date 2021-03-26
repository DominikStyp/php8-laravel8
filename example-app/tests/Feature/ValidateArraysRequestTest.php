<?php

namespace Tests\Feature;

use App\Http\Requests\ValidateArraysRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidateArraysRequestTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_validation_of_array()
    {
        $params = [
            'users' => [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]
        ];
        $request = ValidateArraysRequest::create('/validate_arrays_request', 'GET', $params);
        $request->setContainer(app());
        $request->validateResolved();
        //$this->assertArrayHasKey("important", $request->getErrors());
        //dump($request->getErrors());


    }
}
