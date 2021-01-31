<?php

namespace App\Providers;

use App\ConcreteClasses\DummyImpl;
use App\Contracts\DummyContract;
use Illuminate\Support\ServiceProvider;

class DummyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            DummyContract::class,
            DummyImpl::class
        );
        $this->app->when(DummyImpl::class)
            ->needs('$version')
            ->give('1.2.3');
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
}
