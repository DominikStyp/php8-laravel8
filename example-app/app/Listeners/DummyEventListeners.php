<?php

namespace App\Listeners;

use App\Events\DummyEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DummyEventListeners
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DummyEvent  $event
     * @return void
     */
    public function handle(DummyEvent $event)
    {
        //
    }
}
