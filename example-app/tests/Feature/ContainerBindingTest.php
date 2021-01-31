<?php

namespace Tests\Feature;

use App\Contracts\DummyContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContainerBindingTest extends TestCase
{

    public function test_example()
    {
        $dummyImpl = $this->app->make(DummyContract::class);
        $this->assertEquals("hello from DummyImpl ver: 1.2.3", $dummyImpl->dummy());
    }
}
