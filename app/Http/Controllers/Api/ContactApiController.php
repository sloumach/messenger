<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ContactApiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $contacts = $user->contacts()->with('contact')->get()->pluck('contact');

        return response()->json($contacts);
    }
}

