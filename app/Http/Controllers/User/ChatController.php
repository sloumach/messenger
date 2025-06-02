<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ChatService;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        $contacts = $this->chatService->getContacts();
        return view('chat.index', compact('contacts'));
    }

    public function show(User $contact)
    {
        $contacts = $this->chatService->getContacts();
        $messages = $this->chatService->getMessagesWith($contact);

        return view('chat.index', compact('contacts', 'contact', 'messages'));
    }

    public function sendMessage(Request $request, User $contact)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $this->chatService->sendMessage($contact, $validated['content']);

        return redirect()->route('chat.show', $contact->id);
    }
}
