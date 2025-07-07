<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Services\FirebaseNotificationService; // 🔁 ajoute ce use

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
            ->with('sender:id,name,email')
            ->where('status', 'pending')
            ->get();
    }

    public function getMessagesWith(User $contact): Collection
    {
        $userId = Auth::id();
        $before = request()->query('before'); // timestamp ou message_id
        $limit = 20;

        $query = Message::where(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $userId)
                ->where('receiver_id', $contact->id);
            })
            ->orWhere(function ($q) use ($userId, $contact) {
                $q->where('sender_id', $contact->id)
                ->where('receiver_id', $userId);
            });

        // Si on veut charger les messages *avant* un certain ID ou date
        if ($before) {
            $query->where('created_at', '<', $before);
        }

        return $query
            ->orderBy('created_at', 'desc') // on trie du plus récent au plus ancien
            ->take($limit)
            ->get()
            ->reverse(); // on les renvoie dans l’ordre du plus ancien au plus récent
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
 // 🔔 Envoi de la notification FCM
        if ($contact->fcm_token) {
            app(FirebaseNotificationService::class)->sendToDevice(
                $contact->fcm_token,
                "Message de ".Auth::user()->name,
                $content
            );
        }
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
