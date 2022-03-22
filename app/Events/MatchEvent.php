<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public function __construct(array $data)
    {
        $this->data = (array) $data;
    }

    public function broadcastOn()
    {
        return new Channel('match-channel');
    }

    public function broadcastWith()
    {
        return $this->data;
    }
}
