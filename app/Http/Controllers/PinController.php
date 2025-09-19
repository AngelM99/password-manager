<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
}
