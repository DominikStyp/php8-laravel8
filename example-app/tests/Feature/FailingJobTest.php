<?php

namespace Tests\Feature;

use App\Jobs\FailingJob;
use App\Jobs\Middleware\MyJobMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FailingJobTest extends TestCase
{

    public function test_failing_job()
    {
        $logFile = storage_path('logs/laravel.log');
        file_put_contents($logFile, ""); // clear log

        FailingJob::dispatch()
            ->onConnection('database')
            ->onQueue('high-priority');

        shell_exec("php artisan queue:work --queue=high-priority,medium-priority,default --stop-when-empty");
        sleep(5);

        $this->assertFileExists($logFile);
        $log = file_get_contents($logFile);

        $this->assertStringContainsString("Job FailingJob has failed: I failed!", $log);

    }
}
