<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SessionFlashVariableTest extends TestCase
{

    public function test_simple_flash_variable()
    {
        $this->setFlash();

        // first attempt to retrieve flash_var
        $response = $this->get(route('get_flash'));
        $response->assertStatus(200);
        $response->assertJson(['flash_var' => 'abc123']);

        // second attempt, variable should be gone
        $response = $this->get(route('get_flash'));
        $response->assertStatus(200);
        $response->assertJsonMissing(['flash_var' => 'abc123']);
    }

    public function test_reflashed_variable()
    {
        $this->setFlash();

        // first attempt to retrieve flash_var WITH reflash
        $response = $this->get(route('get_flash', ['reflash' => true]));
        $response->assertStatus(200);
        $response->assertJson(['flash_var' => 'abc123']);

        // second attempt should have the variable too
        $response = $this->get(route('get_flash'));
        $response->assertStatus(200);
        $response->assertJson(['flash_var' => 'abc123']);

        // THIRD attempt without reflashing, variable should be gone
        $response = $this->get(route('get_flash'));
        $response->assertStatus(200);
        $response->assertJsonMissing(['flash_var' => 'abc123']);
    }

    public function test_keep_variable()
    {
        $this->setFlash();

        // first attempt to retrieve flash_var WITH reflash
        $response = $this->get(route('get_flash', ['keep' => true]));
        $response->assertStatus(200);
        $response->assertJson(['flash_var' => 'abc123']);

        // second attempt should have the variable too
        $response = $this->get(route('get_flash'));
        $response->assertStatus(200);
        $response->assertJson(['flash_var' => 'abc123']);

        // THIRD attempt without reflashing, variable should be gone
        $response = $this->get(route('get_flash'));
        $response->assertStatus(200);
        $response->assertJsonMissing(['flash_var' => 'abc123']);
    }

    public function test_now_variable()
    {
        $response = $this->get(route('set_flash', ['now' => true]));
        $response->assertStatus(200);
        $response->assertJson(['flash_var' => 'abc123']);

        // second attempt from ->now() should result in failure
        $response = $this->get(route('get_flash'));
        $response->assertStatus(200);
        $response->assertJsonMissing(['flash_var' => 'abc123']);

    }

    private function setFlash($args = [])
    {
        $response = $this->get(route('set_flash', $args));
        $response->assertStatus(200);
        $response->assertJson(['flash_var' => 'abc123']);
    }
}
