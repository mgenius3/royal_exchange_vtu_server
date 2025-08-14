<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getOrCreateChat(Request $request)
    {
        $user = Auth::user();
        $chat = Chat::firstOrCreate(
            ['user_id' => $user->id],
            ['has_unread' => false]
        );

        return response()->json([
            'chat_id' => $chat->id,
            'messages' => $chat->messages()->orderBy('created_at', 'asc')->get(),
        ], 200);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        $chat = Chat::find($request->chat_id);

        // Additional safety check (though validation ensures chat exists)
        if (!$chat) {
            return response()->json(['message' => 'Chat not found'], 404);
        }

        if ($chat->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'chat_id' => $request->chat_id, // Fixed: Use chat_id from request
            'sender_type' => 'user',
            'content' => $request->content,
            'is_read' => false,
        ]);

        // Mark chat as having unread messages
        $chat->update(['has_unread' => true]);

        return response()->json(['message' => $message], 201);
    }

    public function getMessages(Request $request, $chatId)
    {
        $user = Auth::user();
        $chat = Chat::findOrFail($chatId);

        if ($chat->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()->orderBy('created_at', 'asc')->get();
        return response()->json(['messages' => $messages], 200);
    }
}