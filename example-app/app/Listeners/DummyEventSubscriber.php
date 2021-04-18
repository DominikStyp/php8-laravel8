<?php

namespace App\Listeners;

use App\Events\DummyEvent;
use App\Events\SimpleBroadcastedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class DummyEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function handleDummyEvent(DummyEvent $event) {
        Log::info("DummyEvent handled from subscriber");
    }

    /**
     * Handle user logout events.
     */
    public function handleSimpleBroadcastedEvent(SimpleBroadcastedEvent $event) {
        Log::info("SimpleBroadcastedEvent handled from subscriber");
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            DummyEvent::class,
            __CLASS__ . '@handleDummyEvent'
        );

        $events->listen(
            SimpleBroadcastedEvent::class,
            __CLASS__ . '@handleSimpleBroadcastedEvent'
        );
    }
}
