<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->defineDefaultRegexConstraintsForAttributes();

        $this->defineCustomResolutions();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    protected function defineDefaultRegexConstraintsForAttributes()
    {
        Route::pattern('YYMMDD', '\d{4}-\d{2}-\d{2}');
    }

    protected function defineCustomResolutions()
    {
        Route::bind("userSlug", function ($value) {
            if(empty($value)){
                return null;
            }
            $words = preg_split("#[-]+#si", $value);
            $builder = User::query();
            foreach($words as $w){
                if(empty($w)){
                    continue;
                }
                $builder->where('name', 'like', "%{$w}%");
            }
            //dd($builder->getQuery()->dump());
            return $builder->firstOrFail();

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
