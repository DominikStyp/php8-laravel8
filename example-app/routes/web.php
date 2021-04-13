<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/upload_files', \App\Http\Controllers\UploadFiles::class);


Route::post('/validate_me', '\App\Http\Controllers\ValidateMeController@post')->name('validate_me_post');
/**
 * Important to note:
 *  - order of attributes MATTERS
 *  -
 */
Route::get('/slug/{productSlug}/{YYMMDD}/{time?}', function(string $productSlug, string $date, $time = '00:00'){
    echo "Product slug:{$productSlug}<br />date: {$date}<br />time: {$time}";
})
    ->where([
        "productSlug" => "[a-zA-Z0-9-]+",
        "time" => "\d{2}:\d{2}"
    ]);

Route::resource('/product', \App\Http\Controllers\ProductController::class);

Route::get('/product_middleware_check', [\App\Http\Controllers\ProductController::class, 'withParamsCheck'])
        ->middleware('product_with_parameters:stock_amount,1');


Route::middleware(['is_admin_by_session'])
    ->get('/is_admin_by_session', function(){
        return new \Illuminate\Http\JsonResponse(['status' => 'ok']);
    });

Route::get('/test_set_flash_var', function(\Illuminate\Http\Request $request){
    $var = 'abc123';
    if($request->has('now')){ // this will ONLY keep the variable for THIS request
        $request->session()->now('flash_var', $var);
        new \Illuminate\Http\JsonResponse(
            ['flash_var' => $request->session()->get('flash_var') ]
        );
    } else {
        $request->session()->flash('flash_var', $var);
    }
    return new \Illuminate\Http\JsonResponse(['flash_var' => $var]);
})->name('set_flash');

Route::get('/test_get_flash_var', function(\Illuminate\Http\Request $request){
        if($request->has('reflash')) {
            $request->session()->reflash(); // keep for additional request
        }
        if($request->has('keep')){
            $request->session()->keep(['flash_var']);
        }
        return new \Illuminate\Http\JsonResponse(['flash_var' => $request->session()->get('flash_var')]);
    })->name('get_flash');


Route::middleware(['is_admin'])
     ->prefix('/admin')
     ->namespace('\App\Http\Controllers\Admin')
     ->group(function (){
         Route::get('/check', 'Check');
     });

// binded route
Route::get("/user/{userSlug}", function(?User $user){
    return $user;
});

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');

Route::get('/validate-me', [App\Http\Controllers\ValidateMeController::class, 'get']);

Route::get('/test_loc/{locale?}', function(\Illuminate\Http\Request $request){
    dump($request->something);
});

Route::get('mailable', function () {
    return (new App\Mail\TestMarkdownEmail());
    // to get HTML as string use:
    // return (new App\Mail\TestMarkdownEmail())->render();
});
