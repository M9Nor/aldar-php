<?php

namespace App\Console\Commands;

use App\User;
use Bouncer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OffboardVendor extends Command
{
    protected $signature = 'aldar:offboard-vendor {--domain=namaa-solutions.com : Email domain of the former vendor}';

    protected $description = 'Disable every account of the former vendor and revoke its roles';

    public function handle(): int
    {
        $domain = ltrim((string) $this->option('domain'), '@');
        $users = User::withDisabled()->withTrashed()->where('email', 'like', '%@' . $domain)->get();

        if ($users->isEmpty()) {
            $this->info("No accounts found for @{$domain}.");
            return 0;
        }

        foreach ($users as $user) {
            Bouncer::sync($user)->roles([]);

            $user->forceFill([
                'password'       => Hash::make(Str::random(64)),
                'remember_token' => null,
                'status'         => 'DISABLED',
                'disabled_at'    => now(),
                'deleted_at'     => now(),
            ])->save();

            $this->line("Disabled #{$user->id} {$user->username} <{$user->email}>");
        }

        Bouncer::refresh();

        return 0;
    }
}
