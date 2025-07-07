<?php

namespace App\Http\Controllers\Api;

use App\Models\Invitation;
use Illuminate\Http\Request;
use App\Services\ChatService;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InvitationApiController extends Controller
{
    protected InvitationService $invitationService;

    public function __construct(InvitationService $invitationService, ChatService $chatService)
    {
        $this->invitationService = $invitationService;
        $this->chatService = $chatService;
    }
    // ✅ Lister les invitations
    public function getInvitations()
    {
        $invitations = $this->chatService->getInvitations();

        return response()->json($invitations);
    }

    // ✅ Envoyer une invitation
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $this->invitationService->sendInvitation($request->email);
            return response()->json(['message' => 'Invitation envoyée avec succès.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // ✅ Accepter une invitation
    public function accept(Invitation $invitation)
    {
        if ($invitation->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $this->invitationService->acceptInvitation($invitation);
        return response()->json(['message' => 'Invitation acceptée.']);
    }

    // ✅ Refuser une invitation
    public function decline(Invitation $invitation)
    {
        if ($invitation->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $this->invitationService->declineInvitation($invitation);
        return response()->json(['message' => 'Invitation refusée.']);
    }
}

