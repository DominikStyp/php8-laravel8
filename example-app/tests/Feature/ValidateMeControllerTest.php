<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ValidateMeControllerTest extends TestCase
{

    public function test_no_validation_errors()
    {
        $response = $this->post(route('validate_me_post',[
            'email' => 'good@email.com',
            'name' => 'user_long_enough'
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSeeText("OK");
    }


    public function test_bad_email_error()
    {
        $response = $this->post(route('validate_me_post',[
            'email' => '@bad.email.com',
            'name' => 'user_long_enough'
        ]));

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasErrors(['email']);

        // test session ViewErrorBag directly
        /** @var ViewErrorBag $errors */
        $errors = session()->get("errors");
        $this->assertEquals("The email must be a valid email address.", $errors->get("email")[0]);
    }

    public function test_bad_email_error_via_AJAX_request()
    {
        $response = $this->json('post', route('validate_me_post',[
            'email' => '@bad.email.com',
            'name' => 'user_long_enough'
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertExactJson([
            'errors' => [
                'email' => [
                    "The email must be a valid email address.",
                    "POST_VALIDATE_ADDED_MESSAGE"
                ]
            ],
            'message' => "The given data was invalid."
        ]);
    }

}
