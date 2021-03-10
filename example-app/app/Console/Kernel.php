<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     *  Exam topics:
     *      Scheduling Artisan Commands
            Scheduling Queue Jobs
            Scheduling Shell Commands
            Time Zones
            Preventing Task Overlaps
            Maintenance Mode
     *
     * Defining scheduler on server via crontab:
     *     * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
     *
     * Testing scheduler locally (running in while loop)
     *     php artisan schedule:work
     *
     * Ping URL functions
            ->pingBefore($url)
            ->thenPing($url)
            ->pingBeforeIf($condition, $url)
            ->thenPingIf($condition, $url)
            ->pingOnSuccess($successUrl)
            ->pingOnFailure($failureUrl);
     *
     *
     *
     * Warning:
     * *) only ->command() and ->exec() tasks can ->runInBackground()
     * *) use ->withoutOverlapping() to prevent start same new task if previous was not finished yet
     * *) ->appendOutputTo() ->emailOutputTo() and ->sendOutputTo()
     *    works ONLY for ->exec() and ->command()
     *
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // calling invokable class
        $schedule->call(new InvokableObj)->everyMinute();
            // WARNING! ->call() doesn't work with output methods
            // ->sendOutputTo(storage_path('logs/task1_tmp.log'))
            // ->appendOutputTo(storage_path('logs/task1.log'));

        // scheduling artisan command with arguments & options @see routes/console.php
        $schedule->command('input-array 5 6 7 -o 10 -o 11 -o 12')->everyMinute()
            ->appendOutputTo(storage_path('logs/task2.log'))
            ->onSuccess(function (){
                Log::info("input-array invoked successfully");
            });
            // ->emailOutputTo('foo@example.com'); you can also e-mail the output

        $schedule->command('non-existent-command')
            ->everyMinute()
            ->onFailure(function (){
            Log::alert("Alert: non-existent-command failed");
        });

        // if you're using command class do as follows
        // $schedule->command(EmailsCommand::class, ['Taylor', '--force'])->daily();

        // scheduling queued jobs
        // $shedule->job(new MyJob)->everyFiveMinutes

        // scheduling shell commands
         $schedule->exec('echo "123" >> ~/exec_command.log')->daily();

         // run even in maintenance mode
        $schedule->exec('echo "456" >> ~/exec_even_in_maitenance_mode.log')
            ->evenInMaintenanceMode()
            ->withoutOverlapping();

        // Run hourly from 8 AM to 5 PM on weekdays...
        $schedule->command('inspire')
            ->weekdays()
            ->hourly()
            ->timezone('America/Chicago')
            ->between('8:00', '17:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
