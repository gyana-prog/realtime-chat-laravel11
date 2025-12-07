<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageData;

    public function __construct(Message $message)
    {
        $this->messageData = [
            'id' => $message->id,
            'sender_id' => $message->meta['sender_id'] ?? $message->user_id,
            'message' => $message->message,
            'created_at' => $message->created_at->toISOString(),
        ];
    }

    public function broadcastOn(): array
    {
        return [new Channel('chat-messages')];
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }

    public function broadcastWith(): array
    {
        return $this->messageData;
    }
}
