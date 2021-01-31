<?php

namespace Tests\Feature;

use App\ConcreteClasses\DummyImplOne;
use App\ConcreteClasses\InjectTest;
use App\ConcreteClasses\ToExtend;
use App\Contracts\DummyContract;
use Illuminate\Container\RewindableGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function Symfony\Component\String\s;

class ContainerBindingTest extends TestCase
{

    public function test_example()
    {
        $dummyImpl = $this->app->make(DummyContract::class);
        $this->assertEquals("hello from DummyImpl ver: 1.2.3", $dummyImpl->dummy());
    }

    public function test_instance_bind()
    {
        $instance = $this->app->make('\App\SomethingDummy');
        $this->assertInstanceOf(\stdClass::class, $instance);
    }

    public function test_contextual_binding()
    {
        /** @var InjectTest $injectTest */
        $injectTest = $this->app->make(InjectTest::class);
        $this->assertEquals(
            "this is dummy one",
            $injectTest->getDummy()
        );
        $this->assertInstanceOf(
            DummyImplOne::class,
            $injectTest->getObject()
        );
    }

    public function test_tagged_resolution() {
        /** @var RewindableGenerator $stds */
        $stds = $this->app->tagged('std');
        $this->assertCount(2, $stds);
        foreach ($stds as $std) {
            $this->assertIsString($std->name);
            $this->assertInstanceOf(\stdClass::class, $std);
            $this->assertCount(3, $std->values);
        }
    }

    public function test_extend(){
        /** @var ToExtend $obj */
        $obj = $this->app->make(ToExtend::class);
        $this->assertEquals("overridden test", $obj->test());
        $this->assertEquals("original", $obj->original());
        $this->assertEquals("original x", $obj->originalX());
        $this->assertEquals("original z", $obj->originalZ());
    }
}
