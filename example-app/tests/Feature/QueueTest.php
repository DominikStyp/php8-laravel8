<?php

namespace Tests\Feature;

use App\Jobs\SlowJobOne;
use App\Jobs\SlowJobTwo;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class QueueTest extends TestCase
{

    public function test_queue()
    {
        $logFile = storage_path('logs/laravel.log');
        file_put_contents($logFile, ""); // clear log

        foreach(range(1, 10) as $nr) {
            SlowJobOne::dispatch(User::findOrFail($nr))->onConnection('database');
            SlowJobTwo::dispatch(Product::findOrFail($nr))->onConnection('database');
        }

        // lets run 4 simultanous workers
        foreach(range(1, 3) as $v) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                //shell_exec("php artisan queue:work --stop-when-empty > NUL");
                sleep(1);
                $command = "start /B php artisan queue:work --stop-when-empty > NUL";
                pclose( popen( $command, 'r' ) );
            } else {
                shell_exec("php artisan queue:work --stop-when-empty > /dev/null &");
            }
        }

        // not let's wait for all the jobs to finish (should be not longer than 6 seconds)
        sleep(25);

        $this->assertFileExists($logFile);
        $log = file_get_contents($logFile);

        foreach(range(1, 10) as $nr) {
            $user = User::findOrFail($nr);
            $product = Product::findOrFail($nr);
            $this->assertStringContainsString("SlowJobOne dispatched, email has been sent to id:{$user->id}, {$user->email}", $log);
            $this->assertStringContainsString("SlowJobTwo dispatched with product: {$product->id}", $log);
        }


    }
}
