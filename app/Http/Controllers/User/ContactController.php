<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ContactController extends Controller
{
    public function __construct(protected ContactService $contactService) {}




    public function destroy($contactId)
    {
        $this->contactService->removeContact($contactId);

        return Redirect::back()->with('success', 'Contact supprimé.');
    }
}

