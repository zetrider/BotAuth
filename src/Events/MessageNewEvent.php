<?php

namespace ZetRider\BotAuth\Events;

use ZetRider\BotAuth\AbstractProvider;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageNewEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $provider;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AbstractProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('botauth');
    }
}
