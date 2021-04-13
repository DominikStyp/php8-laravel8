<?php

namespace App\Http\Middleware;

use App\Models\Product;
use Closure;
use Illuminate\Http\Request;

class ProductWithParameters
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next, string $param, $value)
    {
        if(Product::where($param, $value)->count() < 1){
            throw new \Exception("No Product with $param = $value");
        }
        return $next($request);
    }
}
