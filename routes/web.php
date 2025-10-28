<?php

use App\Http\Controllers\CredentialController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');

Route::get('/auth/google/callback', function () {
    $user = Socialite::driver('google')->user();

    $existingUser = User::where('email', $user->email)->first();

    if ($existingUser) {
        Auth::login($existingUser, true);
    } else {
        $newUser = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => Hash::make(Str::random(16)),  // Contraseña random, ya que usa Google
        ]);
        Auth::login($newUser, true);
    }

    return redirect('/dashboard');
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.register');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Credentials routes
    Route::get('/credentials', [CredentialController::class, 'index'])->name('credentials.index');
    Route::post('/credentials', [CredentialController::class, 'store'])->name('credentials.store');
    Route::put('/credentials/{credential}', [CredentialController::class, 'update'])->name('credentials.update');
    Route::delete('/credentials/{credential}', [CredentialController::class, 'destroy'])->name('credentials.destroy');
    Route::post('/credentials/{credential}/verify-pin', [CredentialController::class, 'verifyPin'])->name('credentials.verify-pin');
    Route::post('/credentials/verify-pin-export', [CredentialController::class, 'verifyPinForExport'])->name('credentials.verify-pin-export');
    Route::post('/credentials/export', [CredentialController::class, 'export'])->name('credentials.export');
    Route::post('/credentials/import', [CredentialController::class, 'import'])->name('credentials.import');
});

// PIN routes
Route::get('/set-pin', [PinController::class, 'showSetPinForm'])->name('set-pin')->middleware('auth');
Route::post('/set-pin', [PinController::class, 'storePin'])->name('set-pin.store')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/change-pin', [PinController::class, 'showChangePinForm'])->name('pin.change');
    Route::post('/change-pin', [PinController::class, 'changePin'])->name('pin.update');
});

// PIN recovery routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/pin/recovery', [PinController::class, 'showRecoveryForm'])->name('pin.recovery');
    Route::post('/pin/recovery', [PinController::class, 'sendRecoveryEmail'])->name('pin.recovery.email');
    Route::get('/pin/reset/{token}', [PinController::class, 'showResetForm'])->name('pin.reset');
    Route::post('/pin/reset', [PinController::class, 'resetPin'])->name('pin.reset.store');
});

require __DIR__ . '/auth.php';
