<?php

namespace Modules\Permissions\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Permissions\Entities\Role;
use Modules\Permissions\Entities\Ability;
use Modules\Permissions\Entities\AbilityGroup;
use App\User;
use Bouncer;

class PermissionsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->createDefaultRoles();
        // $this->createDefaultPermissions();

        // $this->createFakeUsers(2, 'ROOT');
        // $this->createFakeUsers(5, 'SUPERADMIN');
        // $this->createFakeUsers(10, 'ADMIN');
        // $this->createFakeUsers(150, 'CLIENT');
    }

    public function createDefaultRoles()
    {
        $root                       = Role::firstOrNew([
            'name' => 'ROOT'
        ]);
        $root->color                = '#FF1654';
        $root->{'title:ar'}         = 'مطور';
        $root->{'title:en'}         = 'Developer';
        $root->save();

        $user                       = User::firstOrNew([
            'email' => config('cms.root.email')
        ]);
        $user->username             = config('cms.root.username');
        $user->verification_code    = config('cms.root.verification_code');
        $user->status               = config('cms.root.status');
        $user->password             = config('cms.root.password');
        $user->save();

        // Assign ROOT role to the developer user.
        Bouncer::sync($user)->roles([$root->name]);

        $superadmin                       = Role::firstOrNew([
            'name' => 'SUPERADMIN'
        ]);
        $superadmin->color                = '#247BA0';
        $superadmin->{'title:ar'}         = 'مدير لوحة التحكم';
        $superadmin->{'title:en'}         = 'Super administrator';
        $superadmin->save();

        $user                       = User::firstOrNew([
            'email' => config('cms.superadmin.email')
        ]);
        $user->username             = config('cms.superadmin.username');
        $user->verification_code    = config('cms.superadmin.verification_code');
        $user->status               = config('cms.superadmin.status');
        $user->password             = config('cms.superadmin.password');
        $user->save();

        // Assign SUPERADMIN role to the superadmin user.
        Bouncer::sync($user)->roles([$superadmin->name]);

        $admin                       = Role::firstOrNew([
            'name' => 'ADMIN'
        ]);
        $admin->color                = '#70C1B3';
        $admin->{'title:ar'}         = 'مسؤول لوحة التحكم';
        $admin->{'title:en'}         = 'Administrator';
        $admin->save();

        $admin->roles()->sync([
            $superadmin->id
        ]);

        $client                       = Role::firstOrNew([
            'name' => 'CLIENT'
        ]);
        $client->color                = '#B2DBBF';
        $client->{'title:ar'}         = 'زبون';
        $client->{'title:en'}         = 'Client';
        $client->save();

        $client->roles()->sync([
            $superadmin->id,
            $admin->id
        ]);
    }

    /**
     * Before creating new permission group seed, please make sure the group isn't already created.
     * If it is, just add the new seeds to the existing group.
     */
    public function createDefaultPermissions()
    {
        $permissionGroups = collect([]);

        // Retrieve super administrator role to assign the seeded abilities.
        $superAdminRole = Role::firstOrNew([
            'name' => 'SUPERADMIN'
        ]);

        // Users: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'USER_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fa fa-fw fa-lg fa-users';
        //     $group->{'title:ar'}    = 'إدارة المستخدمين';
        //     $group->{'title:en'}    = 'User Management';
        //     $group->save();

        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'users.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'users.view_summaries' => [
        //                 'ar' => [
        //                     'title' => 'عرض المختصر',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Summary',
        //                 ]
        //             ],
        //             'users.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'users.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'users.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'users.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'users.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'users.force_delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف نهائي',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete Permanently',
        //                 ]
        //             ],
        //         ]
        //     ]);
        // }
        // Roles: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'ROLE_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fa fa-fw fa-lg fa-user-tie';
        //     $group->{'title:ar'}    = 'إدارة الأدوار';
        //     $group->{'title:en'}    = 'Role Management';
        //     $group->save();

        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'roles.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'roles.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'roles.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'roles.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'roles.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'roles.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'roles.force_delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف نهائي',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete Permanently',
        //                 ]
        //             ],
        //         ]
        //     ]);
        // }
        // Categories: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'CATEGORY_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fa fa-fw fa-lg fa-list-ul';
        //     $group->{'title:ar'}    = 'إدارة التصنيفات';
        //     $group->{'title:en'}    = 'Category Management';
        //     $group->save();

        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'categories.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'categories.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'categories.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'categories.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'categories.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'categories.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'categories.force_delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف نهائي',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete Permanently',
        //                 ]
        //             ],
        //         ]
        //     ]);
        // }
        // Configs: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'CONFIG_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fab fa-fw fa-lg fa-whmcs';
        //     $group->{'title:ar'}    = 'إدارة الإعدادات';
        //     $group->{'title:en'}    = 'Configs Management';
        //     $group->save();

        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'configs.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'configs.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'configs.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'configs.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'configs.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'configs.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'configs.force_delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف نهائي',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete Permanently',
        //                 ]
        //             ],
        //         ]
        //     ]);
        // }
        Tags: {
            $group                  = AbilityGroup::firstOrNew([
                'name' => 'TAG_MANAGEMENT'
            ]);
            $group->icon            = 'fa fa-fw fa-lg fa-tags';
            $group->{'title:ar'}    = 'إدارة الوسوم';
            $group->{'title:en'}    = 'Tag Management';
            $group->save();

            $permissionGroups->push([
                'group'         => $group,
                'permissions'   => [
                    'tags.view' => [
                        'ar' => [
                            'title' => 'عرض',
                        ],
                        'en' => [
                            'title' => 'View',
                        ]
                    ],
                    'tags.create' => [
                        'ar' => [
                            'title' => 'إضافة',
                        ],
                        'en' => [
                            'title' => 'Create',
                        ]
                    ],
                    'tags.edit' => [
                        'ar' => [
                            'title' => 'تعديل',
                        ],
                        'en' => [
                            'title' => 'Edit',
                        ]
                    ],
                    'tags.delete' => [
                        'ar' => [
                            'title' => 'حذف',
                        ],
                        'en' => [
                            'title' => 'Delete',
                        ]
                    ],
                    'tags.view_delete' => [
                        'ar' => [
                            'title' => 'عرض المحذوفات',
                        ],
                        'en' => [
                            'title' => 'View Deleted Items',
                        ]
                    ],
                    'tags.restore' => [
                        'ar' => [
                            'title' => 'استعادة',
                        ],
                        'en' => [
                            'title' => 'Restore',
                        ]
                    ],
                    'tags.force_delete' => [
                        'ar' => [
                            'title' => 'حذف نهائي',
                        ],
                        'en' => [
                            'title' => 'Delete Permanently',
                        ]
                    ],
                    'tags.cache' => [
                        'ar' => [
                            'title' => 'مسح التخزين المؤقت',
                        ],
                        'en' => [
                            'title' => 'cache',
                        ]
                    ],
                    'tags.requests' => [
                        'ar' => [
                            'title' => 'الطلبات',
                        ],
                        'en' => [
                            'title' => 'Orders',
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

    public function createFakeUsers($count, $role)
    {
        factory(User::class, $count)->create()->each(function($user) use ($role) {
            $user->assign($role);
        });
    }
}
