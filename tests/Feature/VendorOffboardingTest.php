<?php

namespace Tests\Feature;

use App\User;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VendorOffboardingTest extends TestCase
{
    use DatabaseTransactions;

    /** Puts the vendor account back into its production state (an e2e run may have offboarded it). */
    private function activeVendor(): User
    {
        $vendor = User::withDisabled()->withTrashed()->where('email', 'root@namaa-solutions.com')->firstOrFail();
        $vendor->forceFill([
            'password'    => Hash::make('vendor-knows-this'),
            'status'      => 'ACTIVE',
            'disabled_at' => null,
            'deleted_at'  => null,
        ])->save();
        Bouncer::assign('ROOT')->to($vendor);

        return $vendor;
    }

    private function otherAccounts(): array
    {
        return DB::table('users')
            ->where('email', 'not like', '%@namaa-solutions.com')
            ->orderBy('id')
            ->get(['id', 'password', 'status', 'disabled_at', 'deleted_at'])
            ->map(function ($row) { return (array) $row; })
            ->all();
    }

    public function test_vendor_accounts_are_disabled_and_stripped_of_roles(): void
    {
        $vendor = $this->activeVendor();
        $this->assertGreaterThan(0, $vendor->roles()->count());

        $this->artisan('aldar:offboard-vendor')->assertExitCode(0);

        $vendor->refresh();
        $this->assertSame('DISABLED', $vendor->status);
        $this->assertNotNull($vendor->disabled_at);
        $this->assertNotNull($vendor->deleted_at);
        $this->assertNull($vendor->remember_token);
        $this->assertFalse(Hash::check('vendor-knows-this', $vendor->password));
        $this->assertSame(0, $vendor->roles()->count());
    }

    public function test_other_accounts_are_untouched(): void
    {
        $this->activeVendor();
        $before = $this->otherAccounts();

        $this->artisan('aldar:offboard-vendor')->assertExitCode(0);

        $this->assertSame($before, $this->otherAccounts());
    }

    public function test_the_root_seeder_does_nothing_without_a_configured_email(): void
    {
        config(['cms.root.email' => null]);
        $count = User::count();

        (new \Modules\Cms\Database\Seeders\UsersTableSeeder())->createRootUser();

        $this->assertSame($count, User::count());
    }
}
