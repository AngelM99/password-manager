<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Credential;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptExistingPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credentials:encrypt-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt all existing plaintext passwords in the credentials table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting password encryption process...');

        // Get all credentials directly from DB to avoid automatic decryption
        $credentials = DB::table('credentials')->get();

        if ($credentials->isEmpty()) {
            $this->info('No credentials found in database.');
            return 0;
        }

        $encrypted = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($credentials as $credential) {
            try {
                // Try to decrypt - if it works, it's already encrypted
                Crypt::decryptString($credential->password);
                $this->line("Credential ID {$credential->id}: Already encrypted, skipping...");
                $skipped++;
            } catch (\Exception $e) {
                // If decryption fails, it's plaintext - encrypt it
                try {
                    $encryptedPassword = Crypt::encryptString($credential->password);
                    DB::table('credentials')
                        ->where('id', $credential->id)
                        ->update(['password' => $encryptedPassword]);

                    $this->info("Credential ID {$credential->id}: Password encrypted successfully");
                    $encrypted++;
                } catch (\Exception $encryptError) {
                    $this->error("Credential ID {$credential->id}: Failed to encrypt - {$encryptError->getMessage()}");
                    $failed++;
                }
            }
        }

        $this->newLine();
        $this->info('Encryption process completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Encrypted', $encrypted],
                ['Already Encrypted (Skipped)', $skipped],
                ['Failed', $failed],
                ['Total', $credentials->count()],
            ]
        );

        return 0;
    }
}
