<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;

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

    public function getInvitations(): Collection
    {
        return Auth::user()
            ->invitations()
            ->where('status', 'pending')
            ->get();
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
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $contact->id,
            'content' => $content,
        ]);
        \Log::info('Message envoyé à', ['receiver_id' => $message->receiver_id]);

        event(new MessageSent($message));

        return $message;
    }
    public function markMessagesAsDeliveredFrom(User $contact): void
    {
        Message::where('sender_id', $contact->id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'sent')
            ->update(['status' => 'delivered']);
    }

    public function markMessagesAsSeenFrom(User $contact): void
    {
        Message::where('sender_id', $contact->id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'delivered')
            ->update(['status' => 'seen']);
    }

}
