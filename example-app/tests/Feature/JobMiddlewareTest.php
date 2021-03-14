<?php

namespace Tests\Feature;

use App\Jobs\SlowJobOne;
use App\Models\User;
use Tests\TestCase;

class JobMiddlewareTest extends TestCase
{

    public function test_job_middleware()
    {
        $logFile = storage_path('logs/laravel.log');
        file_put_contents($logFile, ""); // clear log

        SlowJobOne::dispatch(User::findOrFail(1))
            ->onConnection('database')
            ->onQueue('high-priority-queue');

        shell_exec("php artisan queue:work --queue=high-priority-queue --stop-when-empty");
        sleep(1);

        // check if middleware was triggered
        $this->assertStringContainsString("MyJobMiddleware invoked before App\Jobs\SlowJobOne", file_get_contents($logFile));
    }
}
