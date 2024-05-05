<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $app_id;
    public $link;
    /**
     * Create a new event instance.
     */
    public function __construct($message, $app_id, $link)
    {
        $this->message = $message;
        $this->app_id = $app_id;
        $this->link = $link;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat')
        ];
    }
    public function broadcastWith(): array
    {


        $notification = [

            "message" => $this->message,
            "app_id" => $this->app_id,
            "link" => $this->link,


        ];

        return ([
            'notification' => $notification
        ]);
    }
}
