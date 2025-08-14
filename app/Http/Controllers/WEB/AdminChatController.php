<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function index()
    {
        $chats = Chat::with('user')->get();
        return view('chats.index', compact('chats'));
    }

    public function show($id)
    {
        $chat = Chat::with(['user', 'messages'])->findOrFail($id);
        return view('chats.show', compact('chat'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $chat = Chat::findOrFail($id);
        Message::create([
            'chat_id' => $chat->id,
            'sender_type' => 'admin',
            'content' => $request->content,
            'is_read' => true, // Admin messages are read by default
        ]);

        // Mark chat as read if all user messages are read
        $hasUnread = $chat->messages()->where('sender_type', 'user')->where('is_read', false)->exists();
        $chat->update(['has_unread' => $hasUnread]);

        return redirect()->route('admin.chats.show', $id)->with('success', 'Reply sent');
    }

    public function markAsRead($id)
    {
        $chat = Chat::findOrFail($id);
        $chat->messages()->where('sender_type', 'user')->where('is_read', false)->update(['is_read' => true]);
        $chat->update(['has_unread' => false]);
        return redirect()->route('admin.chats.show', $id)->with('success', 'Messages marked as read');
    }

    public function destroy($id)
    {
        $chat = Chat::findOrFail($id);
        $chat->delete(); // Deletes chat and associated messages (due to cascade)

        return redirect()->route('admin.chats.index')->with('success', 'Chat deleted successfully');
    }
}