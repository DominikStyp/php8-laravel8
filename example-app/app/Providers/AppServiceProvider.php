<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::include('includes.myforelse', 'myforelse');

        // extending blade via custom directives
        Blade::directive('user_email', function($id){
            return "<?php echo \App\Models\User::findOrFail($id)->email ?>";
        });

        Blade::directive('jsonize', function(){
            return '<?php ob_start(); ?>';
        });

        Blade::directive('endjsonize', function(){
            return '<?php echo json_encode(trim(ob_get_clean())); ?>';
        });

        // extending blades adding custom if`s
        Blade::if('env', function ($environment) {
            return app()->environment($environment);
        });





    }
}
