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

    /**
     * Verify PIN before export
     */
    public function verifyPinForExport(Request $request)
    {
        // Rate limiting: max 5 attempts per minute
        $key = 'verify-pin-export:' . Auth::id();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'Demasiados intentos. Por favor intente de nuevo en ' . $seconds . ' segundos.'
            ], 429);
        }

        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        // Verify PIN
        if (!Hash::check($request->pin, Auth::user()->pin_hash)) {
            RateLimiter::hit($key, 60);
            return response()->json([
                'success' => false,
                'message' => 'El PIN ingresado es incorrecto.'
            ], 401);
        }

        // Clear rate limiter on successful verification
        RateLimiter::clear($key);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Export credentials to encrypted file
     */
    public function export(Request $request)
    {
        $request->validate([
            'encryption_password' => 'required|string|min:8',
        ]);

        $credentials = Auth::user()->credentials()->latest()->get();

        // Validar que haya credenciales para exportar
        if ($credentials->isEmpty()) {
            return redirect()->route('credentials.index')
                ->with('error', 'No tienes credenciales para exportar.');
        }

        $exportData = [
            'export_date' => now()->toIso8601String(),
            'credentials' => $credentials->map(function ($credential) {
                return [
                    'title' => $credential->title,
                    'username' => $credential->username,
                    'password' => $credential->password,
                    'url' => $credential->url,
                    'notes' => $credential->notes,
                    'created_at' => $credential->created_at->toIso8601String(),
                ];
            })
        ];

        // Convertir a JSON
        $jsonData = json_encode($exportData);

        // Encriptar con AES-256-CBC
        $encryptionPassword = $request->encryption_password;

        // Generar una clave de 32 bytes (256 bits) a partir de la contraseña usando PBKDF2
        $salt = random_bytes(16);
        $iterations = 10000;
        $key = hash_pbkdf2('sha256', $encryptionPassword, $salt, $iterations, 32, true);

        // Generar un IV aleatorio
        $iv = random_bytes(16);

        // Encriptar los datos
        $encryptedData = openssl_encrypt(
            $jsonData,
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        // Crear el paquete final: version|salt|iv|encrypted_data
        $package = base64_encode('v1') . '|' .
                   base64_encode($salt) . '|' .
                   base64_encode($iv) . '|' .
                   base64_encode($encryptedData);

        $filename = 'credentials_backup_' . now()->format('Y-m-d_His') . '.encrypted';

        return response($package)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import credentials from JSON or encrypted file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());

            // Verificar si es un archivo encriptado
            $isEncrypted = strpos($file->getClientOriginalName(), '.encrypted') !== false;

            if ($isEncrypted) {
                // Desencriptar el archivo
                if (!$request->has('decryption_password')) {
                    return redirect()->back()->with('error', 'Se requiere la contraseña de desencriptación.');
                }

                $decryptionPassword = $request->decryption_password;

                // Separar el paquete: version|salt|iv|encrypted_data
                $parts = explode('|', $content);

                if (count($parts) !== 4) {
                    return redirect()->back()->with('error', 'El archivo encriptado está corrupto o no es válido.');
                }

                $version = base64_decode($parts[0]);
                $salt = base64_decode($parts[1]);
                $iv = base64_decode($parts[2]);
                $encryptedData = base64_decode($parts[3]);

                // Verificar versión
                if ($version !== 'v1') {
                    return redirect()->back()->with('error', 'Versión de archivo no compatible.');
                }

                // Derivar la clave usando la misma configuración que en export
                $iterations = 10000;
                $key = hash_pbkdf2('sha256', $decryptionPassword, $salt, $iterations, 32, true);

                // Desencriptar
                $jsonData = openssl_decrypt(
                    $encryptedData,
                    'aes-256-cbc',
                    $key,
                    OPENSSL_RAW_DATA,
                    $iv
                );

                if ($jsonData === false) {
                    return redirect()->back()->with('error', 'Contraseña de desencriptación incorrecta o archivo corrupto.');
                }

                $data = json_decode($jsonData, true);

                if (!$data || json_last_error() !== JSON_ERROR_NONE) {
                    return redirect()->back()->with('error', 'El archivo desencriptado no contiene datos válidos.');
                }
            } else {
                // Archivo JSON sin encriptar (compatibilidad con archivos antiguos)
                $data = json_decode($content, true);

                // Validar que el JSON sea válido
                if (!$data || json_last_error() !== JSON_ERROR_NONE) {
                    return redirect()->back()->with('error', 'El archivo JSON no es válido o está corrupto.');
                }
            }

            // Validar estructura principal del archivo
            $requiredFields = ['credentials'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return redirect()->back()->with('error', 'El formato del archivo no es correcto. Falta el campo: ' . $field);
                }
            }

            // Validar que credentials sea un array
            if (!is_array($data['credentials'])) {
                return redirect()->back()->with('error', 'El formato del archivo no es correcto. Las credenciales deben ser un array.');
            }

            // Validar que el archivo no esté vacío
            if (empty($data['credentials'])) {
                return redirect()->back()->with('error', 'El archivo no contiene credenciales para importar.');
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($data['credentials'] as $index => $credentialData) {
                // Validar campos requeridos en cada credencial
                $requiredCredentialFields = ['title', 'username', 'password'];
                $missingFields = [];

                foreach ($requiredCredentialFields as $field) {
                    if (!isset($credentialData[$field]) || empty(trim($credentialData[$field]))) {
                        $missingFields[] = $field;
                    }
                }

                if (!empty($missingFields)) {
                    $errors[] = "Credencial #" . ($index + 1) . " tiene campos faltantes: " . implode(', ', $missingFields);
                    continue;
                }

                // Verificar si ya existe una credencial con el mismo título y usuario
                $exists = Auth::user()->credentials()
                    ->where('title', $credentialData['title'])
                    ->where('username', $credentialData['username'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Crear la credencial
                try {
                    Auth::user()->credentials()->create([
                        'title' => $credentialData['title'],
                        'username' => $credentialData['username'],
                        'password' => $credentialData['password'],
                        'url' => $credentialData['url'] ?? null,
                        'notes' => $credentialData['notes'] ?? null,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error al importar credencial #" . ($index + 1) . ": " . $e->getMessage();
                }
            }

            // Si no se importó nada
            if ($imported === 0 && $skipped === 0) {
                $errorMessage = 'No se pudo importar ninguna credencial.';
                if (!empty($errors)) {
                    $errorMessage .= ' Errores: ' . implode('; ', $errors);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            $message = "Importación completada: {$imported} credenciales importadas";
            if ($skipped > 0) {
                $message .= ", {$skipped} duplicadas omitidas";
            }
            if (!empty($errors)) {
                $message .= ". Advertencias: " . implode('; ', $errors);
            }

            return redirect()->route('credentials.index')->with('success', $message . '.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}
