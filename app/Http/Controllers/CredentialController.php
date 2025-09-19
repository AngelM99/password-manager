<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CredentialController extends Controller
{
    public function index()
    {
        $credentials = Auth::user()->credentials;
        return view('credentials.index', compact('credentials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'url' => 'nullable|string',
        ]);

        $credential = Auth::user()->credentials()->create([
            'title' => $request->title,
            'username' => $request->username,
            'password' => $request->password,
            'url' => $request->url,
        ]);

        return redirect()->route('credentials.index')->with('status', 'Credencial guardada exitosamente.');
    }
}
