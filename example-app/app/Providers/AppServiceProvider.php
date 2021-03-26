<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
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

        // implicit means the rule will apply also to empty strings
        // simple ::extend will only apply to NOT empty strings
        Validator::extendImplicit('dominik_rule', function ($attribute, $value, $parameters, $validator) {
            /** @var $validator \Illuminate\Validation\Validator */
            $validator->setCustomMessages([
                'dominik_rule' => 'Dominik rule does not apply to :attribute field, LOL'
            ]);
            $validator->after(function($validatorObj){
                /** @var $validatorObj \Illuminate\Validation\Validator */
                 if(! $validatorObj->valid()) {
                     $validatorObj->messages()->add('dominik_rule', ':attribute .... and this is AFTER message rule');
                 }
            });

            return $value == 'this_is_dominik_rule';
        });





    }
}
