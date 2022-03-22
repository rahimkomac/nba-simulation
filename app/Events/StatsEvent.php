<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new Channel('stats-channel');
    }

    public function broadcastWith()
    {
        return $this->data;
    }
}
