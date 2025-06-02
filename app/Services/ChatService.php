<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ChatService
{
    public function getContacts(): Collection
    {
        return Auth::user()
            ->contacts()
            ->with('contact')
            ->get()
            ->pluck('contact');
    }

    public function getMessagesWith(User $contact): Collection
    {
        $userId = Auth::id();

        return Message::where(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $contact->id);
            })
            ->orWhere(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $contact->id)
                  ->where('receiver_id', $userId);
            })
            ->orderBy('created_at')
            ->get();
    }

    public function sendMessage(User $contact, string $content): Message
    {
        return Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $contact->id,
            'content' => $content,
        ]);
    }
}
