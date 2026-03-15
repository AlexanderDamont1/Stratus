<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class SessionTokenUpdated implements ShouldBroadcastNow
{
    public $userId;
    public $token;

    public function __construct($userId, $token)
    {
        $this->userId = $userId;
        $this->token  = $token;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'session.updated';
    }
}
