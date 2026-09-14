<?php

namespace Modules\Cms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->createRootUser();
    }

    public function createRootUser()
    {
        $user                       = User::firstOrNew([
            'email' => config('cms.root.email')
        ]);
        $user->username             = config('cms.root.username');
        $user->verification_code    = config('cms.root.verification_code');
        $user->status               = config('cms.root.status');
        $user->password             = config('cms.root.password');
        $user->save();
    }
}
