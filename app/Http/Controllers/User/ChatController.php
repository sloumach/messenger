<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ChatService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        return $this->loadDashboard();
    }

    // Afficher les messages avec un contact
    public function show(User $contact)
    {
        if (!Auth::user()->contacts()->where('contact_id', $contact->id)->exists()) {
            abort(403, 'Unauthorized');
        }

        return $this->loadDashboard($contact);
    }

    // 🧠 Méthode centralisée pour charger le dashboard
    private function loadDashboard(?User $contact = null)
    {
        $contacts = $this->chatService->getContacts();
        $invitations = $this->chatService->getInvitations();

        $messages = null;
        if ($contact) {
            $messages = $this->chatService->getMessagesWith($contact);
        }

        return view('dashboard', compact('contacts', 'invitations', 'contact', 'messages'));
    }

    public function sendMessage(Request $request, User $contact)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        if (!Auth::user()->contacts()->where('contact_id', $contact->id)->exists()) {
            abort(403, 'Unauthorized');
        } //check if the contact is in the user's contacts

        $this->chatService->sendMessage($contact, $validated['content']);

        return redirect()->route('chat.show', $contact->id);
    }

    public function markAsDelivered(User $contact)
    {
        $this->chatService->markMessagesAsDeliveredFrom($contact);

        return response()->json(['status' => 'ok']);
    }

    public function markAsSeen(User $contact)
    {
        $this->chatService->markMessagesAsSeenFrom($contact);

        return response()->json(['status' => 'ok']);
    }

}
