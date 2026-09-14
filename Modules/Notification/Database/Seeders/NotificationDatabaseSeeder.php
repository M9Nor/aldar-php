<?php

namespace Modules\Notification\Database\Seeders;
use Modules\Notification\Entities\FirebaseNotification;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Permissions\Entities\Role;
use Modules\Permissions\Entities\Ability;
use Modules\Permissions\Entities\AbilityGroup;
use App\User;
use Bouncer;

class NotificationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        // $this->SeedSystemNotifications();
        $this->createPermissions();

    }

    // protected function SeedSystemNotifications()
    // {
    //     $Notification = FirebaseNotification::firstOrNew([
    //         'type' => 'NEW_USER',
    //     ]);
    //     $Notification->{'title:ar'} = 'هناك  مستخدم جديد';
    //     $Notification->{'body:ar'}  = 'لقد تم إضافة مستخدم جديد {{request_username}}.';
    //     $Notification->{'icon:ar'}  = 'logo.png';
    //     $Notification->{'sound:ar'} = 'default.mp3';
    //     $Notification->save();
    // }

    public function createPermissions()
    {
        $permissionGroups = collect([]);

        dd('worked');
        // Retrieve super administrator role to assign the seeded abilities.
        $superAdminRole = Role::firstOrNew([
            'name' => 'SUPERADMIN'
        ]);

        Notifications: {
            $group                  = AbilityGroup::firstOrNew([
                'name' => 'NOTIFICATION_MANAGEMENT'
            ]);
            $group->icon            = 'fa fa-fw fa-lg fa-bell';
            $group->{'title:ar'}    = 'إدارة الإشعارات';
            $group->{'title:en'}    = 'Notification Management';
            $group->save();

            $permissionGroups->push([
                'group'         => $group,
                'permissions'   => [
                    'notifications.view' => [
                        'ar' => [
                            'title' => 'عرض',
                        ],
                        'en' => [
                            'title' => 'View',
                        ]
                    ],
                    'notifications.create' => [
                        'ar' => [
                            'title' => 'إضافة وإرسال',
                        ],
                        'en' => [
                            'title' => 'Create',
                        ]
                    ],
                ]
            ]);
        }

        foreach($permissionGroups as $group)
        {
            foreach($group['permissions'] as $code => $translations)
            {
                $permission = $group['group']->abilities()->updateOrCreate([
                    'name' => $code
                ]);

                $permission->fill($translations);
                $permission->save();

                $superAdminRole->allow($code);
            }

        }
        // Refresh the permissions cache.
        Bouncer::refresh();
    }
}
