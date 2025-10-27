<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Carbon\Carbon;

class PinController extends Controller
{
    public function showSetPinForm()
    {
        return view('auth.set-pin');
    }

    public function storePin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
            'pin_confirmation' => 'required|same:pin',
        ]);

        $user = Auth::user();
        $user->pin_hash = Hash::make($request->pin);
        $user->save();

        return redirect()->route('dashboard')->with('status', 'PIN configurado exitosamente.');
    }

    /**
     * Show change PIN form
     */
    public function showChangePinForm()
    {
        return view('auth.change-pin');
    }

    /**
     * Change PIN with current PIN validation
     */
    public function changePin(Request $request)
    {
        $request->validate([
            'current_pin' => 'required|digits:4',
            'pin' => 'required|digits:4|different:current_pin',
            'pin_confirmation' => 'required|same:pin',
        ]);

        $user = Auth::user();

        // Verify current PIN
        if (!Hash::check($request->current_pin, $user->pin_hash)) {
            throw ValidationException::withMessages([
                'current_pin' => ['El PIN actual es incorrecto.'],
            ]);
        }

        // Update PIN
        $user->pin_hash = Hash::make($request->pin);
        $user->save();

        return redirect()->route('dashboard')->with('success', 'PIN actualizado exitosamente.');
    }

    /**
     * Show PIN recovery request form
     */
    public function showRecoveryForm()
    {
        return view('auth.pin-recovery');
    }

    /**
     * Send PIN recovery email
     */
    public function sendRecoveryEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate recovery token
        $token = Str::random(60);

        // Store token in database (reusing password_reset_tokens table)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Send email with recovery link
        try {
            Mail::send('emails.pin-recovery', ['token' => $token, 'email' => $request->email, 'user' => $user], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Recuperación de PIN - Password Manager');
            });

            return back()->with('success', 'Se ha enviado un enlace de recuperación a tu correo electrónico.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar el correo. Por favor intenta de nuevo.');
        }
    }

    /**
     * Show reset PIN form with token
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-pin', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Reset PIN with token
     */
    public function resetPin(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'pin' => 'required|digits:4',
            'pin_confirmation' => 'required|same:pin',
        ]);

        // Check if token exists and is not expired (15 minutes)
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'email' => ['No se encontró una solicitud de recuperación para este correo.'],
            ]);
        }

        // Check if token is expired (15 minutes)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            throw ValidationException::withMessages([
                'token' => ['El enlace de recuperación ha expirado. Por favor solicita uno nuevo.'],
            ]);
        }

        // Verify token
        if (!Hash::check($request->token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['El enlace de recuperación es inválido.'],
            ]);
        }

        // Update PIN
        $user = User::where('email', $request->email)->first();
        $user->pin_hash = Hash::make($request->pin);
        $user->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'PIN restablecido exitosamente. Por favor inicia sesión.');
    }
}
