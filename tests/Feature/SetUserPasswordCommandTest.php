<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SetUserPasswordCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sets_the_password_from_hidden_prompts(): void
    {
        $this->artisan('aldar:set-password', ['username' => 'parity-admin'])
            ->expectsQuestion('New password (min 12 characters)', 'correct horse battery')
            ->expectsQuestion('Repeat the new password', 'correct horse battery')
            ->assertExitCode(0);

        $this->assertTrue(Hash::check('correct horse battery', User::where('username', 'parity-admin')->first()->password));
    }

    public function test_rejects_short_and_mismatched_passwords(): void
    {
        $original = User::where('username', 'parity-admin')->first()->password;

        $this->artisan('aldar:set-password', ['username' => 'parity-admin'])
            ->expectsQuestion('New password (min 12 characters)', 'short')
            ->assertExitCode(1);

        $this->artisan('aldar:set-password', ['username' => 'parity-admin'])
            ->expectsQuestion('New password (min 12 characters)', 'correct horse battery')
            ->expectsQuestion('Repeat the new password', 'something different')
            ->assertExitCode(1);

        $this->assertSame($original, User::where('username', 'parity-admin')->first()->password);
    }

    public function test_unknown_account_fails(): void
    {
        $this->artisan('aldar:set-password', ['username' => 'nobody-here'])->assertExitCode(1);
    }
}
