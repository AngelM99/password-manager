<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class CredentialController extends Controller
{
    public function index(Request $request)
    {
        $credentials = Auth::user()->credentials()->latest()->get();

        return view('credentials.index', compact('credentials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $credential = Auth::user()->credentials()->create([
            'title' => $request->title,
            'username' => $request->username,
            'password' => $request->password,
            'url' => $request->url,
            'notes' => $request->notes,
        ]);

        return redirect()->route('credentials.index')->with('success', 'Credencial guardada exitosamente.');
    }

    public function update(Request $request, Credential $credential)
    {
        // Ensure user owns this credential
        if ($credential->user_id !== Auth::id()) {
            abort(403, 'No autorizado.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $credential->update([
            'title' => $request->title,
            'username' => $request->username,
            'password' => $request->password,
            'url' => $request->url,
            'notes' => $request->notes,
        ]);

        return redirect()->route('credentials.index')->with('success', 'Credencial actualizada exitosamente.');
    }

    public function destroy(Credential $credential)
    {
        // Ensure user owns this credential
        if ($credential->user_id !== Auth::id()) {
            abort(403, 'No autorizado.');
        }

        $credential->delete();

        return redirect()->route('credentials.index')->with('success', 'Credencial eliminada exitosamente.');
    }

    /**
     * Verify PIN and return credential password
     */
    public function verifyPin(Request $request, Credential $credential)
    {
        // Rate limiting: max 5 attempts per minute
        $key = 'verify-pin:' . Auth::id();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'pin' => ['Demasiados intentos. Por favor intente de nuevo en ' . $seconds . ' segundos.'],
            ]);
        }

        // Ensure user owns this credential
        if ($credential->user_id !== Auth::id()) {
            abort(403, 'No autorizado.');
        }

        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        // Verify PIN
        if (!Hash::check($request->pin, Auth::user()->pin_hash)) {
            RateLimiter::hit($key, 60); // Lock for 60 seconds after 5 failed attempts

            throw ValidationException::withMessages([
                'pin' => ['El PIN ingresado es incorrecto.'],
            ]);
        }

        // Clear rate limiter on successful verification
        RateLimiter::clear($key);

        // Return the decrypted password
        return response()->json([
            'success' => true,
            'password' => $credential->password,
        ]);
    }
}
