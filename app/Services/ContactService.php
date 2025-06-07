<?php
namespace App\Services;

use App\Models\Contact;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class ContactService
{

    public function removeContact(int $contactId): void
    {
        $userId = Auth::id();

        // Supprimer le contact
        Contact::where('user_id', $userId)
            ->where('contact_id', $contactId)
            ->delete();

        // Supprimer aussi le contact inverse (symétrique)
        Contact::where('user_id', $contactId)
            ->where('contact_id', $userId)
            ->delete();

        // Supprimer l'invitation acceptée (dans les deux sens)
        Invitation::where(function ($query) use ($userId, $contactId) {
            $query->where('sender_id', $userId)->where('receiver_id', $contactId);
        })->orWhere(function ($query) use ($userId, $contactId) {
            $query->where('sender_id', $contactId)->where('receiver_id', $userId);
        })->where('status', 'accepted')->delete();
    }

}
