<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use App\Models\Invitation;
use App\Models\User;

class InvitationService
{
    public function sendInvitation(string $email): void
    {
        $receiver = User::where('email', $email)->firstOrFail();
        $sender   = Auth::user();

        if ($sender->id === $receiver->id) {
            dd("Vous ne pouvez pas vous inviter vous-même.");
            throw new \Exception("Vous ne pouvez pas vous inviter vous-même.");
        }

        if (Invitation::where('sender_id', $sender->id)->where('receiver_id', $receiver->id)->exists()) {
            dd("Invitation déjà envoyée.");
            throw new \Exception("Invitation déjà envoyée.");
        }

        Invitation::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'status'      => 'pending',
        ]);
    }

    public function acceptInvitation(Invitation $invitation): void
    {
        $invitation->update(['status' => 'accepted']);

        // Créer la relation dans la table contacts
        Contact::firstOrCreate([
            'user_id'    => $invitation->sender_id,
            'contact_id' => $invitation->receiver_id,
        ]);

        Contact::firstOrCreate([
            'user_id'    => $invitation->receiver_id,
            'contact_id' => $invitation->sender_id,
        ]);
    }

    public function declineInvitation(Invitation $invitation): void
    {
        $invitation->update(['status' => 'declined']);
    }
}
