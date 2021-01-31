<?php

namespace App\Providers;

use App\ConcreteClasses\DummyImpl;
use App\ConcreteClasses\DummyImplOne;
use App\ConcreteClasses\InjectTest;
use App\Contracts\DummyContract;
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
}
