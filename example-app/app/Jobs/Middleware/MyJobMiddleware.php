<?php


namespace App\Jobs\Middleware;


use Illuminate\Support\Facades\Log;

class MyJobMiddleware
{
    public function handle($job, $next)
    {
        $reflection = new \ReflectionClass($job);
        Log::info("MyJobMiddleware invoked before ". $reflection->getName() );
        return $next($job);
    }
}
