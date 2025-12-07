<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Jobs\ProcessMessageJob;

class MessageController extends Controller
{
  public function store(Request $request)
{
    $validated = $request->validate([
        'sender_id' => 'required|integer|min:1|max:999',
        'message' => 'required|string|max:500',
    ]);

    $message = Message::create([
        'message' => $validated['message'],
        'meta' => ['sender_id' => $validated['sender_id']],
    ]);

    // 🔥 DISPATCH QUEUE JOB!
    ProcessMessageJob::dispatch($message);

    return response()->json([
        'message_id' => $message->id,
        'status' => 'queued for processing',
        'sender_id' => $validated['sender_id']
    ], 201);
}

}
