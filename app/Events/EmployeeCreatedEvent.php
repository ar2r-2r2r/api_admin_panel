<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $user_id;
    public int $manager_id;

    /**
     * Create a new event instance.
     */
    public function __construct($user_id, $manager_id)
    {
        $this->user_id = $user_id;
        $this->manager_id = $manager_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
