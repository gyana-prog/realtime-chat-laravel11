<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Message $message) {}

    public function handle(): void
    {
        // Only process - NO broadcast here!
        $cleanMessage = strip_tags($this->message->message);
        
        $meta = [
            'processed_at' => now()->toISOString(),
            'sender_id' => $this->message->meta['sender_id'] ?? 1,
        ];

        $this->message->update([
            'message' => $cleanMessage,
            'meta' => $meta
        ]);

        \Log::info('Message processed: ' . $this->message->id);
    }
}
