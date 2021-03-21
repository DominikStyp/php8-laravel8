<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SimpleBroadcastedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $information;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        Log::info("Event created");
        $this->information = "Hello from SimpleBroadcastedEvent "
            . date("Y-m-d H:i:s");
    }

    /**
     * WARNING! Events are not broadcasted immediatly
     * They are stored in the JOBS QUEUE (database or other driver)
     * To broadcast them you must run: php artisan queue:work (or have worker enabled
     *
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('dominik-channel');
    }

    public function broadcastAs()
    {
        return 'simple-broadcast-event';
    }
}
