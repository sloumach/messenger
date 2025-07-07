<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;

class MessageApiController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    // 🟢 Récupérer les messages avec un contact donné
    public function show(User $contact)
    {
        $messages = $this->chatService->getMessagesWith($contact);

        return response()->json(array_values($messages->toArray())); // ✅ corrige le problème
    }

    // 🟢 Envoyer un message à un contact
    public function store(Request $request, User $contact)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = $this->chatService->sendMessage($contact, $request->content);

        return response()->json(['message' => 'Message envoyé', 'data' => $message], 201);
    }

    // 🟢 Marquer les messages comme "delivered"
    public function markDelivered(User $contact)
    {
        $this->chatService->markMessagesAsDeliveredFrom($contact);

        return response()->json(['status' => 'delivered']);
    }

    // 🟢 Marquer les messages comme "seen"
    public function markSeen(User $contact)
    {
        $this->chatService->markMessagesAsSeenFrom($contact);

        return response()->json(['status' => 'seen']);
    }
}
