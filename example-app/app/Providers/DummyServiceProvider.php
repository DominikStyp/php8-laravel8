<?php

namespace App\Providers;

use App\ConcreteClasses\DummyImpl;
use App\ConcreteClasses\DummyImplOne;
use App\ConcreteClasses\InjectTest;
use App\ConcreteClasses\ToExtend;
use App\Contracts\DummyContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use stdClass;

class DummyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindings();
        $this->whenNeedsBind();
        $this->tagging();
        $this->extends();
        $this->containerEvents();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function bindings(): void {
        //bind interface
        $this->app->bind(
            DummyContract::class,
            DummyImpl::class
        );
        //bind scalar variable
        $this->app->when(DummyImpl::class)
            ->needs('$version')
            ->give('1.2.3');
        //bind existing instance
        $this->app->instance('\App\SomethingDummy', new stdClass());
    }

    private function whenNeedsBind(): void {
        // if InjectTest class needs DummyContract in the constructor
        // we give DummyImplOne
        $this->app
            ->when(InjectTest::class)
            ->needs(DummyContract::class)
            ->give(function () {
                return new DummyImplOne('1.1.1');
            });
    }

    private function tagging(): void {

        $this->app->bind('StdOne', function () {
            $x = new stdClass();
            $x->name = "one";
            $x->values = [1, 2, 3];
            return $x;
        });

        $this->app->bind('StdTwo', function () {
            $x = new stdClass();
            $x->name = "two";
            $x->values = [4, 5, 6];
            return $x;
        });

        $this->app->tag([
            'StdOne',
            'StdTwo',
        ], ['std']);
    }

    private function extends(): void {
        $this->app->extend(
    ToExtend::class,
            function($service, $app){
                  // retrieve z property via reflection (protected)
                $reflection = new \ReflectionClass($service);
                $property = $reflection->getProperty('z');
                $property->setAccessible(true);
                $zValue = $property->getValue($service);

                return new class($zValue) extends ToExtend {
                    public function test(): string {
                        return "overridden test";
                    }
                    public function originalX(): string {
                        return $this->x;
                    }
                    public function originalZ(): string {
                        return $this->z;
                    }
                };
            }
        );
    }
    private function containerEvents()
    {
        // for object of any type
        $this->app->resolving(function ($object, $app){
            $str = is_object($object) ? get_class($object) : $object;
            //echo $str; // Log::info("Resolving " . $str );
        });
        // for specific object
        $this->app->resolving(
            DummyContract::class,
            function ($object, $app){
            $str =  is_object($object) ? get_class($object) : $object;
            Log::info("Resolving DummyContract: " . $str);
        });
    }
}
