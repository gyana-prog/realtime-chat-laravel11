<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Jobs\ProcessMessageJob;
use App\Events\MessageReceived;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|integer|min:1|max:999',
            'message' => 'required|string|max:500',
        ]);

        $message = Message::create([
            'user_id' => 1,
            'message' => $validated['message'],
            'meta' => ['sender_id' => $validated['sender_id']],
        ]);

        // Queue for processing (sanitize, metadata)
        ProcessMessageJob::dispatch($message);

        // 🔥 BROADCAST HERE (toOthers works in Controller!)
        broadcast(new MessageReceived($message))->toOthers();

        return response()->json([
            'message_id' => $message->id,
            'status' => 'queued',
            'sender_id' => $validated['sender_id']
        ], 201);
    }
}
