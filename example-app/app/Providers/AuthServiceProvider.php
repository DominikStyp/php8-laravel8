<?php

namespace App\Providers;

use App\Dummy\Guards\DummyGuard;
use App\Dummy\UserProviders\DummyUserProvider;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();


        Auth::extend('dummy-guard-driver', function ($app, $name, array $config) {
            //return new DummyGuard(Auth::createUserProvider($config['provider']));
            return new DummyGuard( new DummyUserProvider() );
        });

        // custom guard: https://laravel.com/docs/6.x/authentication#closure-request-guards
        Auth::viaRequest('dummy-request-driver', function (Request $request) {
            if($request->input('secret') === 'aaa') {
                Log::info("User logged in via dummy-guard");
                return User::find(1)->first();
            }
            Log::info("Request data: ", $request->all());
            return null;
        });

    }
}
