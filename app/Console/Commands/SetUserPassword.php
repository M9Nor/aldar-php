<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetUserPassword extends Command
{
    protected $signature = 'aldar:set-password {username : Username or email of the account}';

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
}
