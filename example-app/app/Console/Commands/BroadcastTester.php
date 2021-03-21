<?php

namespace App\Console\Commands;

use App\Events\PrivateBroadcastedEvent;
use App\Events\SimpleBroadcastedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Console\Command;

class BroadcastTester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast-tester';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcasts test event';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        event(new SimpleBroadcastedEvent());
        event(new PrivateBroadcastedEvent());
        $this->info("Events stored in the jobs queue (remember to have queue worker running to process jobs)");
        return 0;
    }
}
