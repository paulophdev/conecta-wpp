<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HelloWorldEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public string $message;

    public function __construct()
    {
        $this->message = 'Olá, mundo!';
        Log::info($this->message);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('dashboard-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'hello-world';
    }
}