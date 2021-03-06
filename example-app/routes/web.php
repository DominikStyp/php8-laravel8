<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

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
