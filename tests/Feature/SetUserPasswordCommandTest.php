<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SetUserPasswordCommandTest extends TestCase
{
    use DatabaseTransactions;

    /** @var array<int, string> */
    private $generatedFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->generatedFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->generatedFiles = [];

        parent::tearDown();
    }

    private function tempCredentialsPath(): string
    {
        $file = sys_get_temp_dir() . '/aldar-set-password-test-' . bin2hex(random_bytes(8)) . '.tsv';
        $this->generatedFiles[] = $file;

        return $file;
    }

    public function test_generate_to_writes_password_to_file_without_printing_it(): void
    {
        $file = $this->tempCredentialsPath();

        // Artisan::call() (rather than $this->artisan()) is used here because
        // it runs against a real BufferedOutput, so Artisan::output() below
        // reflects everything actually printed by the command.
        $exitCode = Artisan::call('aldar:set-password', ['username' => 'parity-admin', '--generate-to' => $file]);
        $this->assertSame(0, $exitCode);

        $this->assertFileExists($file);
        $this->assertSame('0600', substr(sprintf('%o', fileperms($file)), -4));

        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $this->assertCount(1, $lines);

        [$timestamp, $username, $password] = explode("\t", $lines[0]);
        $this->assertSame('parity-admin', $username);
        $this->assertSame(32, strlen($password));
        $this->assertNotFalse(strtotime($timestamp));

        $fresh = User::where('username', 'parity-admin')->first();
        $this->assertTrue(Hash::check($password, $fresh->password));

        $output = Artisan::output();
        $this->assertStringNotContainsString($password, $output);
        $this->assertStringContainsString("Password for #{$fresh->id} parity-admin written to {$file}.", $output);
    }

    public function test_generate_to_appends_and_keeps_earlier_lines(): void
    {
        $file = $this->tempCredentialsPath();

        $this->artisan('aldar:set-password', ['username' => 'parity-admin', '--generate-to' => $file])
            ->assertExitCode(0);
        $firstLine = file($file, FILE_IGNORE_NEW_LINES)[0];

        $this->artisan('aldar:set-password', ['username' => 'parity-admin', '--generate-to' => $file])
            ->assertExitCode(0);

        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $this->assertCount(2, $lines);
        $this->assertSame($firstLine, $lines[0]);
        $this->assertNotSame($lines[0], $lines[1]);
    }

    public function test_generate_to_unknown_account_fails_and_leaves_no_file(): void
    {
        $file = $this->tempCredentialsPath();

        $this->artisan('aldar:set-password', ['username' => 'nobody-here', '--generate-to' => $file])
            ->assertExitCode(1);

        $this->assertFileNotExists($file);
    }

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

    public function test_disabled_or_deleted_accounts_are_not_found_even_via_username(): void
    {
        $vendor = User::withDisabled()->withTrashed()->where('email', 'root@namaa-solutions.com')->firstOrFail();
        $knownHash = Hash::make('vendor-knows-this');
        $vendor->forceFill([
            'password'    => $knownHash,
            'disabled_at' => now(),
            'deleted_at'  => now(),
        ])->save();

        $this->artisan('aldar:set-password', ['username' => 'developer'])->assertExitCode(1);
        $this->artisan('aldar:set-password', ['username' => 'root@namaa-solutions.com'])->assertExitCode(1);

        $reloaded = User::withDisabled()->withTrashed()->where('email', 'root@namaa-solutions.com')->firstOrFail();
        $this->assertSame($knownHash, $reloaded->password);
    }
}
