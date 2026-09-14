<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SetUserPassword extends Command
{
    protected $signature = 'aldar:set-password {username : Username or email of the account} {--generate-to= : Generate a random password and append it to this file (created with mode 0600) instead of prompting}';

    protected $description = 'Set a new password for an account; the value is prompted and never echoed';

    public function handle(): int
    {
        $identity = (string) $this->argument('username');
        $user = User::where(function ($query) use ($identity) {
            $query->where('username', $identity)->orWhere('email', $identity);
        })->first();

        if (! $user) {
            $this->error("No account found for {$identity}.");
            return 1;
        }

        $generateTo = $this->option('generate-to');
        if ($generateTo !== null) {
            return $this->generateToFile($user, (string) $generateTo);
        }

        $password = (string) $this->secret('New password (min 12 characters)');
        if (mb_strlen($password) < 12) {
            $this->error('The password must be at least 12 characters.');
            return 1;
        }

        if ($password !== (string) $this->secret('Repeat the new password')) {
            $this->error('The passwords do not match.');
            return 1;
        }

        $user->forceFill([
            'password'       => Hash::make($password),
            'remember_token' => null,
        ])->save();

        $this->info("Password updated for #{$user->id} {$user->username}.");

        return 0;
    }

    private function generateToFile(User $user, string $file): int
    {
        $password = Str::random(32);

        if (! $this->ensureCredentialsFile($file)) {
            $this->error("Could not prepare the credentials file {$file}.");
            return 1;
        }

        $line = sprintf("%s\t%s\t%s\n", gmdate('Y-m-d\TH:i:s\Z'), $user->username, $password);

        if (! $this->appendLocked($file, $line)) {
            $this->error("Could not write to the credentials file {$file}.");
            return 1;
        }

        $user->forceFill([
            'password'       => Hash::make($password),
            'remember_token' => null,
        ])->save();

        $this->info("Password for #{$user->id} {$user->username} written to {$file}.");

        return 0;
    }

    /**
     * Make sure the credentials file (and its parent directory) exist with
     * restrictive permissions before anything is appended to it.
     */
    private function ensureCredentialsFile(string $file): bool
    {
        $dir = dirname($file);

        if (! is_dir($dir)) {
            if (! @mkdir($dir, 0700, true) && ! is_dir($dir)) {
                return false;
            }
        }

        if (! file_exists($file)) {
            $handle = @fopen($file, 'c');
            if ($handle === false) {
                return false;
            }
            fclose($handle);
        }

        return @chmod($file, 0600);
    }

    private function appendLocked(string $file, string $line): bool
    {
        $handle = @fopen($file, 'ab');
        if ($handle === false) {
            return false;
        }

        $written = false;
        if (flock($handle, LOCK_EX)) {
            $written = fwrite($handle, $line) !== false;
            flock($handle, LOCK_UN);
        }
        fclose($handle);

        return $written;
    }
}
