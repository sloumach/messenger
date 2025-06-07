<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvitationController extends Controller
{
    use AuthorizesRequests;
    public function __construct(protected InvitationService $invitationService) {}

    // Envoyer une invitation
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $this->invitationService->sendInvitation($request->email);
            return redirect()->route('chat.index')->with('success', 'Invitation envoyée avec succès.');
            //return Redirect::back()->with('success', 'Invitation envoyée avec succès.');

        } catch (\Exception $e) {
            return redirect()->route('chat.index')->with('error', $e->getMessage());
            //return Redirect::back()->with('error', $e->getMessage());
        }
    }

    // Accepter une invitation
    public function accept(Invitation $invitation)
    {
        $this->authorize('handle', $invitation); // optionnel : security

        $this->invitationService->acceptInvitation($invitation);

        return Redirect::back()->with('success', 'Invitation acceptée.');
    }

    // Refuser une invitation
    public function decline(Invitation $invitation)
    {
        $this->authorize('handle', $invitation); // optionnel : security

        $this->invitationService->declineInvitation($invitation);

        return Redirect::back()->with('success', 'Invitation refusée.');
    }
}

