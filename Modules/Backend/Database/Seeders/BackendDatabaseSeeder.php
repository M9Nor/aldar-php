<?php

namespace Modules\Backend\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Backend\Entities\ContactUS;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Permissions\Entities\Role;
use Modules\Permissions\Entities\Ability;
use Modules\Permissions\Entities\AbilityGroup;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\Config;
use Modules\Cms\Entities\Content;
use App\User;
use Bouncer;

class BackendDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->createPermissions();
        // $this->createPropertyClassifications ();
        // $this->createPropertyStatus();
        // $this->createPropertyFeatures();
        // $this->createFacilities();
        // $this->createContentCategory();
        // $this->createContractsCategory();
        // $this->createPayingCategory();
        // $this->createPriceCategory();
        // $this->createContactUsRequest(); // Causes an error. Commented because it no longer important.
        // $this->createFilter();
        // $this->createAgent();
        // $this->createCurrency();
        // $this->createOffice();
        // $this->createConfig();
        // $this->createStats();
    }

    public function createPermissions()
    {
        $permissionGroups = collect([]);

        // Retrieve super administrator role to assign the seeded abilities.
        $superAdminRole = Role::firstOrNew([
            'name' => 'SUPERADMIN'
        ]);

        foreach(Category::types() as $type => $info)
        {
            $singular = Str::singular($type);
            $group                  = AbilityGroup::firstOrNew([
                'name' => strtoupper($singular).'_CATS_MANAGEMENT'
            ]);
            $group->icon            = 'fa fa-fw fa-lg fa-sitemap';
            $group->{'title:ar'}    = 'إدارة التصنيفات ['.Category::getTypeTitle($type).']';
            $group->{'title:en'}    = 'Categories ['.Str::title($singular).' Management]';
            $group->save();

            $permissionGroups->push([
                'group'       => $group,
                'permissions' => [
                    'categories.'.strtolower($type).'.view' => [
                        'ar' => [
                            'title' => 'عرض',
                        ],
                        'en' => [
                            'title' => 'View',
                        ]
                    ],
                    'categories.'.strtolower($type).'.create' => [
                        'ar' => [
                            'title' => 'إضافة',
                        ],
                        'en' => [
                            'title' => 'Create',
                        ]
                    ],
                    'categories.'.strtolower($type).'.edit' => [
                        'ar' => [
                            'title' => 'تعديل',
                        ],
                        'en' => [
                            'title' => 'Edit',
                        ]
                    ],
                    'categories.'.strtolower($type).'.delete' => [
                        'ar' => [
                            'title' => 'حذف',
                        ],
                        'en' => [
                            'title' => 'Delete',
                        ]
                    ],
                    'categories.'.strtolower($type).'.delete_translation' => [
                        'ar' => [
                            'title' => 'حذف الترجمة',
                        ],
                        'en' => [
                            'title' => 'Delete a Translation',
                        ]
                    ],
                    'categories.'.strtolower($type).'.view_delete' => [
                        'ar' => [
                            'title' => 'عرض المحذوفات',
                        ],
                        'en' => [
                            'title' => 'View Deleted Items',
                        ]
                    ],
                    'categories.'.strtolower($type).'.restore' => [
                        'ar' => [
                            'title' => 'استعادة',
                        ],
                        'en' => [
                            'title' => 'Restore',
                        ]
                    ],
                    'categories.'.strtolower($type).'.force_delete' => [
                        'ar' => [
                            'title' => 'حذف نهائي',
                        ],
                        'en' => [
                            'title' => 'Delete Permanently',
                        ]
                    ],
                ]
            ]);
        }
        foreach(Content::types() as $type => $info)
        {
            $singular = Str::singular($type);
            $group                  = AbilityGroup::firstOrNew([
                'name' => strtoupper($singular).'_MANAGEMENT'
            ]);
            $group->icon            = 'fa fa-fw fa-lg fa-layer-group';
            $group->{'title:ar'}    = 'إدارة المحتوى ['.Content::getTypeTitle($type).']';
            $group->{'title:en'}    = 'Contents ['.Str::title($singular).' Management]';
            $group->save();

            $permissionGroups->push([
                'group'       => $group,
                'permissions' => [
                    'contents.'.strtolower($type).'.view' => [
                        'ar' => [
                            'title' => 'عرض',
                        ],
                        'en' => [
                            'title' => 'View',
                        ]
                    ],
                    'contents.'.strtolower($type).'.create' => [
                        'ar' => [
                            'title' => 'إضافة',
                        ],
                        'en' => [
                            'title' => 'Create',
                        ]
                    ],
                    'contents.'.strtolower($type).'.edit' => [
                        'ar' => [
                            'title' => 'تعديل',
                        ],
                        'en' => [
                            'title' => 'Edit',
                        ]
                    ],
                    'contents.'.strtolower($type).'.delete' => [
                        'ar' => [
                            'title' => 'حذف',
                        ],
                        'en' => [
                            'title' => 'Delete',
                        ]
                    ],
                    'contents.'.strtolower($type).'.delete_translation' => [
                        'ar' => [
                            'title' => 'حذف الترجمة',
                        ],
                        'en' => [
                            'title' => 'Delete a Translation',
                        ]
                    ],
                    'contents.'.strtolower($type).'.view_delete' => [
                        'ar' => [
                            'title' => 'عرض المحذوفات',
                        ],
                        'en' => [
                            'title' => 'View Deleted Items',
                        ]
                    ],
                    'contents.'.strtolower($type).'.restore' => [
                        'ar' => [
                            'title' => 'استعادة',
                        ],
                        'en' => [
                            'title' => 'Restore',
                        ]
                    ],
                    'contents.'.strtolower($type).'.force_delete' => [
                        'ar' => [
                            'title' => 'حذف نهائي',
                        ],
                        'en' => [
                            'title' => 'Delete Permanently',
                        ]
                    ],
                    'contents.'.strtolower($type).'.enable' => [
                        'ar' => [
                            'title' => 'تمكين',
                        ],
                        'en' => [
                            'title' => 'Enable',
                        ]
                    ],
                    'contents.'.strtolower($type).'.disable' => [
                        'ar' => [
                            'title' => 'تعطيل',
                        ],
                        'en' => [
                            'title' => 'Disable',
                        ]
                    ],

                ]
            ]);
        }
        // Projects: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'PROJECT_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fa fa-fw fa-lg fa-list-ul';
        //     $group->{'title:ar'}    = 'إدارة المشاريع';
        //     $group->{'title:en'}    = 'Project Management';
        //     $group->save();
        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'projects.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'projects.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'projects.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'projects.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'projects.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'projects.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'projects.force_delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف نهائي',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete Permanently',
        //                 ]
        //             ],
        //             'projects.delete_translation' => [
        //                 'ar' => [
        //                     'title' => 'حذف الترجمة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete a Translation',
        //                 ]
        //             ],
        //             'projects.enable' => [
        //                 'ar' => [
        //                     'title' => 'تمكين',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Enable',
        //                 ]
        //             ],
        //             'projects.disable' => [
        //                 'ar' => [
        //                     'title' => 'تعطيل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Disable',
        //                 ]
        //             ],
        //             'projects.special' => [
        //                 'ar' => [
        //                     'title' => 'تمييز المشاريع',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Special',
        //                 ]
        //             ],
        //             'projects.not_special' => [
        //                 'ar' => [
        //                     'title' => 'الغاء تمييز المشاريع',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Notspecial',
        //                 ]
        //             ],
        //             'projects.company_and_project' => [
        //                 'ar' => [
        //                     'title' => 'شركة الإنشاء واسم المشروع الحقيقي',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Notspecial',
        //                 ]
        //             ],
        //         ]
        //     ]);
        // }
        // Areas: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'AREA_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fa fa-fw fa-lg fa-map-marked-alt';
        //     $group->{'title:ar'}    = 'إدارة المناطق';
        //     $group->{'title:en'}    = 'Area Management';
        //     $group->save();

        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'areas.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'areas.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'areas.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'areas.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'areas.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'areas.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'areas.force_delete' => [
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
        // Cities: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'CITIES_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fa fa-fw fa-lg fa-map-marked-alt';
        //     $group->{'title:ar'}    = 'إدارة المدن';
        //     $group->{'title:en'}    = 'Cities Management';
        //     $group->save();

        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'cities.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'cities.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'cities.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'cities.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'cities.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'cities.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'cities.force_delete' => [
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
        // LandingPages: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'LANDING_PAGES_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fa fa-fw fa-lg fa-map-marked-alt';
        //     $group->{'title:ar'}    = 'إدارة Landing Pages';
        //     $group->{'title:en'}    = 'Landing Pages Management';
        //     $group->save();

        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'landingpages.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'landingpages.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'landingpages.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'landingpages.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'landingpages.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'landingpages.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'landingpages.force_delete' => [
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
        // Countries: {
        //     $group                  = AbilityGroup::firstOrNew([
        //         'name' => 'COUNTRIES_MANAGEMENT'
        //     ]);
        //     $group->icon            = 'fa fa-fw fa-lg fa-map-marked-alt';
        //     $group->{'title:ar'}    = 'إدارة الدول';
        //     $group->{'title:en'}    = 'Countries Management';
        //     $group->save();

        //     $permissionGroups->push([
        //         'group'         => $group,
        //         'permissions'   => [
        //             'countries.view' => [
        //                 'ar' => [
        //                     'title' => 'عرض',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View',
        //                 ]
        //             ],
        //             'countries.create' => [
        //                 'ar' => [
        //                     'title' => 'إضافة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Create',
        //                 ]
        //             ],
        //             'countries.edit' => [
        //                 'ar' => [
        //                     'title' => 'تعديل',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Edit',
        //                 ]
        //             ],
        //             'countries.delete' => [
        //                 'ar' => [
        //                     'title' => 'حذف',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Delete',
        //                 ]
        //             ],
        //             'countries.view_delete' => [
        //                 'ar' => [
        //                     'title' => 'عرض المحذوفات',
        //                 ],
        //                 'en' => [
        //                     'title' => 'View Deleted Items',
        //                 ]
        //             ],
        //             'countries.restore' => [
        //                 'ar' => [
        //                     'title' => 'استعادة',
        //                 ],
        //                 'en' => [
        //                     'title' => 'Restore',
        //                 ]
        //             ],
        //             'countries.force_delete' => [
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
    public function createPropertyClassifications (){
        $parentCategory                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Residential')
        ]);
        $parentCategory->parent_id            = null;
        $parentCategory->{'title:ar'}         = 'عقارات سكنية';
        $parentCategory->{'title:en'}         = 'Residential';
        $parentCategory->{'description:ar'}   = 'عقارات سكنية';
        $parentCategory->{'description:en'}   = 'Residential';
        $parentCategory->{'brief:ar'}         = 'عقارات سكنية';
        $parentCategory->{'brief:en'}         = 'Residential';
        $parentCategory->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Apartments')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'شقق';
        $category->{'title:en'}         = 'Apartments';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Hotel Appartments')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'شقق فندقية';
        $category->{'title:en'}         = 'Hotel Appartments';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Flat')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'شقة';
        $category->{'title:en'}         = 'Flat';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Residence')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'شقة ريزيدانس';
        $category->{'title:en'}         = 'Residence';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Houses')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بيوت';
        $category->{'title:en'}         = 'Houses';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Villa')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'فلل';
        $category->{'title:en'}         = 'Villa';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Duplex')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'دوبلكس';
        $category->{'title:en'}         = 'Duplex';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Triplex')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'تريبلكس';
        $category->{'title:en'}         = 'Triplex';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Country house')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'منزل ريفي';
        $category->{'title:en'}         = 'Country house';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Farm house')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بيت ريفي';
        $category->{'title:en'}         = 'Farm house';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Farmhouse')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بيت مزرعة';
        $category->{'title:en'}         = 'Farmhouse';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('palaces')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'قصر';
        $category->{'title:en'}         = 'palaces';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('mansion')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'قصور';
        $category->{'title:en'}         = 'mansion';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('House')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'منزل';
        $category->{'title:en'}         = 'House';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Homes')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'منازل';
        $category->{'title:en'}         = 'Homes';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();


        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('cotareae')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'كوخ';
        $category->{'title:en'}         = 'cotareae';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Bungalow')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بنغل';
        $category->{'title:en'}         = 'Bungalow';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Summer houses')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بيوت اصطياف';
        $category->{'title:en'}         = 'Summer houses';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Summer house')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بيت صيفي';
        $category->{'title:en'}         = 'Summer house';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Summer villas')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'فلل اصطياف';
        $category->{'title:en'}         = 'Summer villas';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Summer villa')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'فيلا صيفية';
        $category->{'title:en'}         = 'Summer villa';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('compounds')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مجمعات';
        $category->{'title:en'}         = 'compounds';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Prefabricated Houses')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بيت مسبق الصنع';
        $category->{'title:en'}         = 'Prefabricated Houses';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Caravan')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'كرفان بيت متنقل';
        $category->{'title:en'}         = 'Caravan';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('realEstatesClassification')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'عقارات';
        $category->{'title:en'}         = 'real estates';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();


        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Cooperative')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مساهمات عقارية حكومية';
        $category->{'title:en'}         = 'Cooperative';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $parentCategory                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Commercial')
        ]);
        $parentCategory->parent_id            = null;
        $parentCategory->{'title:ar'}         = 'عقارات تجارية';
        $parentCategory->{'title:en'}         = 'Commercial';
        $parentCategory->{'description:ar'}   = 'عقارات تجارية';
        $parentCategory->{'description:en'}   = 'Commercial';
        $parentCategory->{'brief:ar'}         = 'عقارات تجارية';
        $parentCategory->{'brief:en'}         = 'Commercial';
        $parentCategory->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Office apartment')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'شقة مكتبية';
        $category->{'title:en'}         = 'Office apartment';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('home office')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مكتب منزلي';
        $category->{'title:en'}         = 'home office';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Offices')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مكاتب';
        $category->{'title:en'}         = 'Offices';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('shared workspaces')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مساحات العمل المشتركة';
        $category->{'title:en'}         = 'shared workspaces';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Shops')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'محلات';
        $category->{'title:en'}         = 'Shops';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Store')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'متجر';
        $category->{'title:en'}         = 'Store';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('showrooms')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'معارض';
        $category->{'title:en'}         = 'showrooms';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Mall')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مول';
        $category->{'title:en'}         = 'Mall_';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('grocery')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بقالة';
        $category->{'title:en'}         = 'grocery';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Bazaar')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بازار';
        $category->{'title:en'}         = 'Bazaar';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('prop_hospital')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مستشفى';
        $category->{'title:en'}         = 'prop_hospital';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('clinic')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'عيادة';
        $category->{'title:en'}         = 'clinic';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Workshop')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'ورشة';
        $category->{'title:en'}         = 'Workshop';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('motel')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'فندق رخيص';
        $category->{'title:en'}         = 'motel';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Hotel')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'فنادق';
        $category->{'title:en'}         = 'Hotel';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('resort')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'منتجع';
        $category->{'title:en'}         = 'resort';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Office complex')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مجمع مكاتب';
        $category->{'title:en'}         = 'Office complex';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Commercial building')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بناء تجاري';
        $category->{'title:en'}         = 'Commercial building';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('internal_Banquet hall')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'قاعة مناسبات';
        $category->{'title:en'}         = 'Banquet hall';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('internal_Events hall')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'صالة مناسبات';
        $category->{'title:en'}         = 'Events hall';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('internal_WEDDING HALL')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'قاعة زفاف';
        $category->{'title:en'}         = 'WEDDING HALL';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('internal_Cafe')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مقهى';
        $category->{'title:en'}         = 'Cafe';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('internal_Car Park')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'موقف سيارات';
        $category->{'title:en'}         = 'Car Park';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('internal_Clinic')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'عيادة';
        $category->{'title:en'}         = 'Clinic';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('internal_Dormitory')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مهجع سكن مشترك';
        $category->{'title:en'}         = 'Dormitory';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('internal_hostel')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'سكن شبابي';
        $category->{'title:en'}         = 'hostel';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('University Housing')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'سكن جامعي';
        $category->{'title:en'}         = 'University Housing';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Garage')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مرآب';
        $category->{'title:en'}         = 'Garage';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Gas stations')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'محطة وقود';
        $category->{'title:en'}         = 'Gas stations';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('factory')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مصنع';
        $category->{'title:en'}         = 'factory';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Plant')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'معمل';
        $category->{'title:en'}         = 'Plant';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('laboratory')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مختبر طبي';
        $category->{'title:en'}         = 'laboratory';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('canteen')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مقصف';
        $category->{'title:en'}         = 'canteen';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('cafeteria')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'كافيتيريا';
        $category->{'title:en'}         = 'cafeteria';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('orchard')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'بستان';
        $category->{'title:en'}         = 'orchard';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('tearoom')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'قهوة شعبية';
        $category->{'title:en'}         = 'tearoom';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Buildings')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'عمارات';
        $category->{'title:en'}         = 'Buildings';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Building')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مبنى';
        $category->{'title:en'}         = 'Building';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $parentCategory                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('lands')
        ]);
        $parentCategory->parent_id            = null;
        $parentCategory->{'title:ar'}         = 'أراضي';
        $parentCategory->{'title:en'}         = 'lands';
        $parentCategory->{'description:ar'}   = 'أراضي';
        $parentCategory->{'description:en'}   = 'lands';
        $parentCategory->{'brief:ar'}         = 'أراضي';
        $parentCategory->{'brief:en'}         = 'lands';
        $parentCategory->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Agricultural project')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مشروع زراعي';
        $category->{'title:en'}         = 'Agricultural project';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Residential lands')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'أراضي سكنية';
        $category->{'title:en'}         = 'Residential lands';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Agricultural lands')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'أراضي زراعية';
        $category->{'title:en'}         = 'Agricultural lands';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_classifications',
            'slug' => Str::slug('Farms')
        ]);
        $category->parent_id            = $parentCategory->id;
        $category->{'title:ar'}         = 'مزارع';
        $category->{'title:en'}         = 'Farms';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

    }
    public function createPropertyStatus(){
        $category                       = Category::firstOrNew([
            'type' => 'property_status',
            'slug' => Str::slug('New')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'جديدة';
        $category->{'title:en'}         = 'New';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_status',
            'slug' => Str::slug('Resale')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'إعادة بيع';
        $category->{'title:en'}         = 'Resale';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_status',
            'slug' => Str::slug('Used')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مستعملة';
        $category->{'title:en'}         = 'Used';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_status',
            'slug' => Str::slug('Ready to move')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'جاهزة';
        $category->{'title:en'}         = 'Ready to move';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_status',
            'slug' => Str::slug('Under Construction')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'قيد الإنشاء';
        $category->{'title:en'}         = 'Under Construction';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_status',
            'slug' => Str::slug('Housing Developments')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'على الخريطة';
        $category->{'title:en'}         = 'Housing Developments';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

    }
    public function createPropertyFeatures(){

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Seaview')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'إطلالة بحر';
        $category->{'title:en'}         = 'Seaview';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Lake view')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'إطلالة بحيرة';
        $category->{'title:en'}         = 'Lake view';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Beach')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'شاطئ';
        $category->{'title:en'}         = 'Beach';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('marina')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مارينا';
        $category->{'title:en'}         = 'marina';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Yacht Marina')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مرسى يخوت';
        $category->{'title:en'}         = 'Yacht Marina';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Adjacent to Sea')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'على البحر مباشرة';
        $category->{'title:en'}         = 'Adjacent to Sea';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Government guarantee')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ضمان حكومي';
        $category->{'title:en'}         = 'Government guarantee';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Rental guarantee')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ضمان تأجير';
        $category->{'title:en'}         = 'Rental guarantee';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Resale guarantee')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ضمان إعادة بيع';
        $category->{'title:en'}         = 'Resale guarantee';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Government Guarantee')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ضمان حكومي';
        $category->{'title:en'}         = 'Government Guarantee';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('For investment')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مناسب للاستثمار';
        $category->{'title:en'}         = 'For investment';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Eigible for bank credit')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مناسب للتمويل البنكي';
        $category->{'title:en'}         = 'Eigible for bank credit';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('suitable for Turkish citizenship')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مناسب للجنسية التركية';
        $category->{'title:en'}         = 'suitable for Turkish citizenship';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Luxury')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'فاخرة';
        $category->{'title:en'}         = 'Luxury';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('cheap')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'رخيصة';
        $category->{'title:en'}         = 'cheap';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('ownership')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'تمليك';
        $category->{'title:en'}         = 'ownership';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Furnished')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مفروشة';
        $category->{'title:en'}         = 'Furnished';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Timeshares')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'عقارات المشاركة بالوقت';
        $category->{'title:en'}         = 'Timeshares';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Residential towers')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'أبراج سكنية';
        $category->{'title:en'}         = 'Residential towers';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Low floors')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'طوابق منخفضة';
        $category->{'title:en'}         = 'Low floors';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Multiple heights')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'متعدد الارتفاعات';
        $category->{'title:en'}         = 'Multiple heights';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Mountain view')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'إطلالة جبل';
        $category->{'title:en'}         = 'Mountain view';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Valley view')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'إطلالة وادي';
        $category->{'title:en'}         = 'Valley view';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Forest view')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'إطلالة غابة';
        $category->{'title:en'}         = 'Forest view';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Green view')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'إطلالة خضراء';
        $category->{'title:en'}         = 'Green view';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Balcony')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'شرفة بلكون';
        $category->{'title:en'}         = 'Balcony';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Master room')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'غرفة ماستر';
        $category->{'title:en'}         = 'Master room';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Parents room')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'غرفة أبوين';
        $category->{'title:en'}         = 'Parents room';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Earthquake resistant')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مقاوم للزلازل';
        $category->{'title:en'}         = 'Earthquake resistant';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Residential complexes')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مجمعات سكنية';
        $category->{'title:en'}         = 'Residential complexes';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Inside a compound')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'داخل مجمع';
        $category->{'title:en'}         = 'Inside a compound';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Open kitchen')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مطبخ مفتوح';
        $category->{'title:en'}         = 'Open kitchen';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Closed kitchen')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مطبخ مغلق';
        $category->{'title:en'}         = 'Closed kitchen';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Independent kitchen')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مطبخ مستقل';
        $category->{'title:en'}         = 'Independent kitchen';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('American kitchen')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مطبخ أمريكي';
        $category->{'title:en'}         = 'American kitchen';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Kitchen with appliances')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مطبخ مع الأجهزة';
        $category->{'title:en'}         = 'Kitchen with appliances';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('Quiet area')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'منطقة هادئة';
        $category->{'title:en'}         = 'Quiet area';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'property_features',
            'slug' => Str::slug('City center')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مركز المدينة';
        $category->{'title:en'}         = 'City center';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();
    }
    public function createFacilities(){
        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('smart homes')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'نظام المنزل الذكي';
        $category->{'title:en'}         = 'smart homes';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Near a metro station')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'قرب المترو';
        $category->{'title:en'}         = 'Near a metro station';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Near the metrobus')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'قرب المتروبوس';
        $category->{'title:en'}         = 'Near the metrobus';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('beside a university')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'قرب جامعة';
        $category->{'title:en'}         = 'beside a university';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Playgrounds')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ملاعب';
        $category->{'title:en'}         = 'Playgrounds';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Sports fields')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ملاعب رياضية';
        $category->{'title:en'}         = 'Sports fields';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Childrens playground')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ملاعب أطفال';
        $category->{'title:en'}         = 'Childrens playground';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('gardens')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'حدائق';
        $category->{'title:en'}         = 'gardens';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Green spaces')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مساحات خضراء';
        $category->{'title:en'}         = 'Green spaces';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Closed parking lots')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مواقف سيارات مغلقة';
        $category->{'title:en'}         = 'Closed parking lots';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Guest parking')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مواقف سيارات للضيوف';
        $category->{'title:en'}         = 'Guest parking';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Private parking')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مواقف سيارات خاصة';
        $category->{'title:en'}         = 'Private parking';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Public parking')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مواقف سيارات عامة';
        $category->{'title:en'}         = 'Public parking';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Open parking')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مواقف سيارات مفتوحة';
        $category->{'title:en'}         = 'Open parking';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Closed swimming pools')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مسابح مغلقة';
        $category->{'title:en'}         = 'Closed swimming pools';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Summer swimming pools')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مسابح صيفية';
        $category->{'title:en'}         = 'Summer swimming pools';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Winter swimming pools')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مسابح شتوية';
        $category->{'title:en'}         = 'Winter swimming pools';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Open swimming pools')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مسابح مفتوحة';
        $category->{'title:en'}         = 'Open swimming pools';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('swimming pools')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مسابح';
        $category->{'title:en'}         = 'swimming pools';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Gym')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'نادي رياضي';
        $category->{'title:en'}         = 'Gym';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Health club')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'نادي صحي';
        $category->{'title:en'}         = 'Health club';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Caffe')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مقهى';
        $category->{'title:en'}         = 'Caffe';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Resturant')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مطعم';
        $category->{'title:en'}         = 'Resturant';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('shopping center')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مركز تسوق';
        $category->{'title:en'}         = 'shopping center';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('mall_internal')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مول';
        $category->{'title:en'}         = 'mall';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('supermarket')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'سوبر ماركت';
        $category->{'title:en'}         = 'supermarket';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Outdoor sports')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ألعاب رياضية خارجية';
        $category->{'title:en'}         = 'Outdoor sports';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Security')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'أمن';
        $category->{'title:en'}         = 'Security';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('security cameras')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'كاميرات مراقبة';
        $category->{'title:en'}         = 'security cameras';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Mosque')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مسجد';
        $category->{'title:en'}         = 'Mosque';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('prayer room')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مصلى';
        $category->{'title:en'}         = 'prayer room';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('School')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مدرسة';
        $category->{'title:en'}         = 'School';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Kindergarten')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'روضة';
        $category->{'title:en'}         = 'Kindergarten';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('nursery')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'حضانة';
        $category->{'title:en'}         = 'nursery';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Turkish bath')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'حمام تركي';
        $category->{'title:en'}         = 'Turkish bath';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('sauna')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ساونا حمام بخار';
        $category->{'title:en'}         = 'sauna';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Jacuzzi')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'جاكوزي';
        $category->{'title:en'}         = 'Jacuzzi';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Shock Room')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'شوك روم';
        $category->{'title:en'}         = 'Shock Room';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('hospital')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مستشفى';
        $category->{'title:en'}         = 'hospital';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('clinic_internal')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'عيادة';
        $category->{'title:en'}         = 'clinic';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Gangway')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ممشى';
        $category->{'title:en'}         = 'Gangway';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Running tracks')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مسارات جري';
        $category->{'title:en'}         = 'Running tracks';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Football field')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ملعب كرة قدم';
        $category->{'title:en'}         = 'Football field';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('basketball court')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'ملعب كرة سلة';
        $category->{'title:en'}         = 'basketball court';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Outdoor sessions')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'جلسات خارجية';
        $category->{'title:en'}         = 'Outdoor sessions';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Banquet hall')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'قاعة مناسبات';
        $category->{'title:en'}         = 'Banquet hall';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Wedding Hall')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'صالة أفراح';
        $category->{'title:en'}         = 'Wedding Hall';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Near a highway')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'قرب طريق سريع';
        $category->{'title:en'}         = 'Near a highway';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Marine transportation')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مواصلات بحرية';
        $category->{'title:en'}         = 'Marine transportation';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Water taxi')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'تكسي بحري';
        $category->{'title:en'}         = 'Water taxi';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Easy transportation')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مواصلات سهلة';
        $category->{'title:en'}         = 'Easy transportation';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Cleaning')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'نظافة';
        $category->{'title:en'}         = 'Cleaning';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Room services')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'خدمة الغرف';
        $category->{'title:en'}         = 'Room services';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Compound management')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'إدارة المجمع';
        $category->{'title:en'}         = 'Compound management';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('dues')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'عائدات الخدمات المشتركة';
        $category->{'title:en'}         = 'dues';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Fast lifts')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مصاعد سريعة';
        $category->{'title:en'}         = 'Fast lifts';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Maintenance Services')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'خدمات الصيانة';
        $category->{'title:en'}         = 'Maintenance Services';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Shared heating')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'تدفئة مشتركة';
        $category->{'title:en'}         = 'Shared heating';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Independent heating combi')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'كومبي تدفئة مستقلة';
        $category->{'title:en'}         = 'Independent heating combi';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Air Conditioners')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'مكيفات';
        $category->{'title:en'}         = 'Air Conditioners';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Central air-conditioning')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'تكييف مركزي';
        $category->{'title:en'}         = 'Central air-conditioning';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Solar energy')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'طاقة شمسية';
        $category->{'title:en'}         = 'Solar energy';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Cinema')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'سينما';
        $category->{'title:en'}         = 'Cinema';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'facilities',
            'slug' => Str::slug('Outdoor Cinema')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'سينما الهواء الطلق';
        $category->{'title:en'}         = 'Outdoor Cinema';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

    }
    // public function createContentCategory(){
    //     $category                       = Category::firstOrNew([
    //         'type' => 'news',
    //         'slug' => Str::slug('General')
    //     ]);
    //     $category->parent_id            = null;
    //     $category->{'title:ar'}         = 'أخبار عامة';
    //     $category->{'title:en'}         = 'General';
    //     $category->{'description:ar'}   = '';
    //     $category->{'description:en'}   = '';
    //     $category->{'brief:ar'}         = '';
    //     $category->{'brief:en'}         = '';
    //     $category->save();

    //     $category                       = Category::firstOrNew([
    //         'type' => 'story',
    //         'slug' => Str::slug('Daily Story')
    //     ]);
    //     $category->parent_id            = null;
    //     $category->{'title:ar'}         = 'ستوري يومي';
    //     $category->{'title:en'}         = 'Daily Story';
    //     $category->{'description:ar'}   = '';
    //     $category->{'description:en'}   = '';
    //     $category->{'brief:ar'}         = '';
    //     $category->{'brief:en'}         = '';
    //     $category->save();

    //     $category                       = Category::firstOrNew([
    //         'type' => 'slider',
    //         'slug' => Str::slug('Slider_up')
    //     ]);
    //     $category->parent_id            = null;
    //     $category->{'title:ar'}         = 'سلايدر علوي';
    //     $category->{'title:en'}         = 'Slider_up';
    //     $category->{'description:ar'}   = '';
    //     $category->{'description:en'}   = '';
    //     $category->{'brief:ar'}         = '';
    //     $category->{'brief:en'}         = '';
    //     $category->save();

    //     $category                       = Category::firstOrNew([
    //         'type' => 'slider',
    //         'slug' => Str::slug('Slider')
    //     ]);
    //     $category->parent_id            = null;
    //     $category->{'title:ar'}         = 'سلايدر';
    //     $category->{'title:en'}         = 'Slider';
    //     $category->{'description:ar'}   = '';
    //     $category->{'description:en'}   = '';
    //     $category->{'brief:ar'}         = '';
    //     $category->{'brief:en'}         = '';
    //     $category->save();

    //     $category                       = Category::firstOrNew([
    //         'type' => 'slider',
    //         'slug' => Str::slug('Slider')
    //     ]);
    //     $category->parent_id            = null;
    //     $category->{'title:ar'}         = 'سلايدر';
    //     $category->{'title:en'}         = 'Slider';
    //     $category->{'description:ar'}   = '';
    //     $category->{'description:en'}   = '';
    //     $category->{'brief:ar'}         = '';
    //     $category->{'brief:en'}         = '';
    //     $category->save();

    //     $category                       = Category::firstOrNew([
    //         'type' => 'faqs',
    //         'slug' => Str::slug('faqs')
    //     ]);
    //     $category->parent_id            = null;
    //     $category->{'title:ar'}         = 'اسئلة متنوعة';
    //     $category->{'title:en'}         = 'faqs';
    //     $category->{'description:ar'}   = '';
    //     $category->{'description:en'}   = '';
    //     $category->{'brief:ar'}         = '';
    //     $category->{'brief:en'}         = '';
    //     $category->save();

    //     $category                       = Category::firstOrNew([
    //         'type' => 'service',
    //         'slug' => Str::slug('service')
    //     ]);
    //     $category->parent_id            = null;
    //     $category->{'title:ar'}         = 'خدمات متنوعة';
    //     $category->{'title:en'}         = 'service';
    //     $category->{'description:ar'}   = '';
    //     $category->{'description:en'}   = '';
    //     $category->{'brief:ar'}         = '';
    //     $category->{'brief:en'}         = '';
    //     $category->save();



    // }
    public function createContractsCategory()
    {
        $category                       = Category::firstOrNew([
            'type' => 'contracts',
            'slug' => Str::slug('For Sale')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'للبيع';
        $category->{'title:en'}         = 'For Sale';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'contracts',
            'slug' => Str::slug('For Rent')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'للأجار';
        $category->{'title:en'}         = 'For Rent';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'contracts',
            'slug' => Str::slug('Daily Rentals')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'للأجار اليومي';
        $category->{'title:en'}         = 'Daily Rentals';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();




    }
    public function createPayingCategory(){
        $Parentcategory                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('Cash')
        ]);
        $Parentcategory->parent_id            = null;
        $Parentcategory->{'title:ar'}         = 'كاش';
        $Parentcategory->{'title:en'}         = 'Cash';
        $Parentcategory->{'description:ar'}   = '';
        $Parentcategory->{'description:en'}   = '';
        $Parentcategory->{'brief:ar'}         = '';
        $Parentcategory->{'brief:en'}         = '';
        $Parentcategory->save();

        $category                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('payments0')
        ]);
        $category->parent_id            = $Parentcategory->id;
        $category->{'title:ar'}         = '0';
        $category->{'title:en'}         = '0';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $Parentcategory                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('Installment')
        ]);
        $Parentcategory->parent_id            = null;
        $Parentcategory->{'title:ar'}         = 'تقسيط';
        $Parentcategory->{'title:en'}         = 'Installment';
        $Parentcategory->{'description:ar'}   = '';
        $Parentcategory->{'description:en'}   = '';
        $Parentcategory->{'brief:ar'}         = '';
        $Parentcategory->{'brief:en'}         = '';
        $Parentcategory->save();

        $category                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('payments12')
        ]);
        $category->parent_id            = $Parentcategory->id;
        $category->{'title:ar'}         = '12';
        $category->{'title:en'}         = '12';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('payments24')
        ]);
        $category->parent_id            = $Parentcategory->id;
        $category->{'title:ar'}         = '24';
        $category->{'title:en'}         = '24';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('payments36')
        ]);
        $category->parent_id            = $Parentcategory->id;
        $category->{'title:ar'}         = '36';
        $category->{'title:en'}         = '36';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('payments48')
        ]);
        $category->parent_id            = $Parentcategory->id;
        $category->{'title:ar'}         = '48';
        $category->{'title:en'}         = '48';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('payments60')
        ]);
        $category->parent_id            = $Parentcategory->id;
        $category->{'title:ar'}         = '60';
        $category->{'title:en'}         = '60';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'payments',
            'slug' => Str::slug('paymentsMore than 60 months')
        ]);
        $category->parent_id            = $Parentcategory->id;
        $category->{'title:ar'}         = 'أكثر من 60 شهراً';
        $category->{'title:en'}         = 'More than 60 months';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();


    }
    public function createPriceCategory(){
        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Roomspayments0')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '0';
        $category->{'title:en'}         = '0';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms1')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '1';
        $category->{'title:en'}         = '1';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms1.5')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '1.5';
        $category->{'title:en'}         = '1.5';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms2')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '2';
        $category->{'title:en'}         = '2';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms2.5')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '2.5';
        $category->{'title:en'}         = '2.5';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms3')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '3';
        $category->{'title:en'}         = '3';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms3.5')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '3.5';
        $category->{'title:en'}         = '3.5';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms4')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '4';
        $category->{'title:en'}         = '4';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms4.5')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '4.5';
        $category->{'title:en'}         = '4.5';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms5')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '5';
        $category->{'title:en'}         = '5';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms5.5')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '5.5';
        $category->{'title:en'}         = '5.5';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('Rooms6')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '6';
        $category->{'title:en'}         = '6';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Rooms',
            'slug' => Str::slug('RoomsMore than 6 rooms')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'أكثر من 6 غرف';
        $category->{'title:en'}         = 'More than 6 rooms';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();
        ////////////////
        $category                       = Category::firstOrNew([
            'type' => 'LivingRooms',
            'slug' => Str::slug('LivingRooms1')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '1';
        $category->{'title:en'}         = '1';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'LivingRooms',
            'slug' => Str::slug('LivingRooms2')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '2';
        $category->{'title:en'}         = '2';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'LivingRooms',
            'slug' => Str::slug('LivingRooms3')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '3';
        $category->{'title:en'}         = '3';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'LivingRooms',
            'slug' => Str::slug('LivingRoomsMore than 3 salons')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'أكثر من 3 صالونات';
        $category->{'title:en'}         = 'More than 3 salons';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();
        ////////////////
        $category                       = Category::firstOrNew([
            'type' => 'Bathrooms',
            'slug' => Str::slug('Bathrooms1')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '1';
        $category->{'title:en'}         = '1';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Bathrooms',
            'slug' => Str::slug('Bathrooms2')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '2';
        $category->{'title:en'}         = '2';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Bathrooms',
            'slug' => Str::slug('Bathrooms3')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '3';
        $category->{'title:en'}         = '3';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Bathrooms',
            'slug' => Str::slug('Bathrooms4')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '4';
        $category->{'title:en'}         = '4';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Bathrooms',
            'slug' => Str::slug('Bathrooms5')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '5';
        $category->{'title:en'}         = '5';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Bathrooms',
            'slug' => Str::slug('Bathrooms6')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '6';
        $category->{'title:en'}         = '6';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'Bathrooms',
            'slug' => Str::slug('BathroomsMore than 6 bathrooms')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'أكثر من 6 حمامات';
        $category->{'title:en'}         = 'More than 6 bathrooms';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();


        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From20000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '20000';
        $category->{'title:en'}         = '20000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From40000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '40000';
        $category->{'title:en'}         = '40000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From50000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '50000';
        $category->{'title:en'}         = '50000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From60000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '60000';
        $category->{'title:en'}         = '60000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From70000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '70000';
        $category->{'title:en'}         = '70000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From80000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '80000';
        $category->{'title:en'}         = '80000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From90000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '90000';
        $category->{'title:en'}         = '90000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From100000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '100000';
        $category->{'title:en'}         = '100000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From150000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '150000';
        $category->{'title:en'}         = '150000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From200000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '200000';
        $category->{'title:en'}         = '200000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From250000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '250000';
        $category->{'title:en'}         = '250000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From300000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '300000';
        $category->{'title:en'}         = '300000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From350000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '350000';
        $category->{'title:en'}         = '350000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From400000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '400000';
        $category->{'title:en'}         = '400000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From500000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '500000';
        $category->{'title:en'}         = '500000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceFrom',
            'slug' => Str::slug('From1000000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '1000000';
        $category->{'title:en'}         = '1000000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        ///////////////
        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto40000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '40000';
        $category->{'title:en'}         = '40000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto50000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '50000';
        $category->{'title:en'}         = '50000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto60000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '60000';
        $category->{'title:en'}         = '60000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto70000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '70000';
        $category->{'title:en'}         = '70000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto80000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '80000';
        $category->{'title:en'}         = '80000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto90000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '90000';
        $category->{'title:en'}         = '90000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto100000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '100000';
        $category->{'title:en'}         = '100000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto150000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '150000';
        $category->{'title:en'}         = '150000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto200000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '200000';
        $category->{'title:en'}         = '200000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto250000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '250000';
        $category->{'title:en'}         = '250000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto300000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '300000';
        $category->{'title:en'}         = '300000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto350000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '350000';
        $category->{'title:en'}         = '350000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto400000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '400000';
        $category->{'title:en'}         = '400000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto500000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '500000';
        $category->{'title:en'}         = '500000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('Upto1000000')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '1000000';
        $category->{'title:en'}         = '1000000';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'PriceUpTo',
            'slug' => Str::slug('UptoMore than 1 million usd')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'أكثر من مليون دولار أمريكي';
        $category->{'title:en'}         = 'More than 1 million usd';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();
        /////////////////////////////////////
        // Spaces from
        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom45')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '45';
        $category->{'title:en'}         = '45';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom50')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '50';
        $category->{'title:en'}         = '50';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom60')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '60';
        $category->{'title:en'}         = '60';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom70')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '70';
        $category->{'title:en'}         = '70';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom80')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '80';
        $category->{'title:en'}         = '80';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom90')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '90';
        $category->{'title:en'}         = '90';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom100')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '100';
        $category->{'title:en'}         = '100';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom120')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '120';
        $category->{'title:en'}         = '120';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom150')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '150';
        $category->{'title:en'}         = '150';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom200')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '200';
        $category->{'title:en'}         = '200';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('Spacefrom300')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = '300';
        $category->{'title:en'}         = '300';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();

        $category                       = Category::firstOrNew([
            'type' => 'SpacesFrom',
            'slug' => Str::slug('SpacefromMore than 300 m2')
        ]);
        $category->parent_id            = null;
        $category->{'title:ar'}         = 'أكثر من 300 م2';
        $category->{'title:en'}         = 'More than 300 m2';
        $category->{'description:ar'}   = '';
        $category->{'description:en'}   = '';
        $category->{'brief:ar'}         = '';
        $category->{'brief:en'}         = '';
        $category->save();
    }
    public function createContactUsRequest(){
        $ContactUs                       = ContactUs::firstOrNew([
            'email' => 'username@support.com'
        ]);
        $ContactUs->sender               = 'عبدالله أل مسعود السعدي';
        $ContactUs->phone                = '+95852852852';
        $ContactUs->description          = 'مرحبا ، أريد تواصل معكم بشأن شراء عقار و الحصول على الجنسية التركية ، أنا وعائلتي';
        $ContactUs->save();

        $ContactUs                       = ContactUs::firstOrNew([
            'email' => 'omara@support.com'
        ]);
        $ContactUs->sender               = 'عمر شيخ نجيب';
        $ContactUs->phone                = '+9522777222';
        $ContactUs->description          = 'أريد عقار استثمر فيه ، لا تقل مساحته عن مليون هكتار';
        $ContactUs->save();

        $ContactUs                       = ContactUs::firstOrNew([
            'email' => 'shee@support.com'
        ]);
        $ContactUs->sender               = 'شيخ نجيب';
        $ContactUs->phone                = '+8888888882';
        $ContactUs->description          = 'عقار كبير المساحة ومطل على البحر و يوجد فيه عدة مسابح';
        $ContactUs->save();
    }
    public function createFilter(){
        $parentCategory                       = Category::firstOrNew([
            'type' => 'real_estate',
            'slug' => Str::slug('project_real_estate')
        ]);
        $parentCategory->parent_id            = null;
        $parentCategory->{'title:ar'}         = 'عقارات تركيا';
        $parentCategory->{'title:en'}         = 'real_estate';
        $parentCategory->{'description:ar'}   = '';
        $parentCategory->{'description:en'}   = '';
        $parentCategory->{'brief:ar'}         = '';
        $parentCategory->{'brief:en'}         = '';
        $parentCategory->save();

        $forSaleCategory = Category::whereSlug('for-sale')->first();
        $forRentCategory = Category::whereSlug('for-rent')->first();
        $seaViewCategory = Category::whereSlug('seaview')->first();
        $ShopsCategory = Category::whereSlug('shops')->first();
        $VillaCategory = Category::whereSlug('villa')->first();
        $FarmsCategory = Category::whereSlug('farms')->first();
        $landsCategory = Category::whereSlug('lands')->first();
        $ApartmentsCategory = Category::whereSlug('apartments')->first();

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('عقارات للبيع في تركيا')
        ]);
        $filter->{'title:ar'}         = 'عقارات للبيع في تركيا';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('عقارات للبيع في اسطنبول')
        ]);
        $filter->{'title:ar'}         = 'عقارات للبيع في اسطنبول';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('عقارات للبيع في بورصة')
        ]);
        $filter->{'title:ar'}         = 'عقارات للبيع في بورصة';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('عقارات للبيع في سكاريا')
        ]);
        $filter->{'title:ar'}         = 'عقارات للبيع في سكاريا';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('عقارات للبيع في يلوا')
        ]);
        $filter->{'title:ar'}         = 'عقارات للبيع في يلوا';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('عقارات للبيع في بوردروم')
        ]);
        $filter->{'title:ar'}         = 'عقارات للبيع في بوردروم';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('عقارات للبيع في طرابزون')
        ]);
        $filter->{'title:ar'}         = 'عقارات للبيع في طرابزون';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('عقارات للبيع في انطاليا')
        ]);
        $filter->{'title:ar'}         = 'عقارات للبيع في انطاليا';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);



        $parentCategory                       = Category::firstOrNew([
            'type' => 'real_estate',
            'slug' => Str::slug('project_istanbul')
        ]);
        $parentCategory->parent_id            = null;
        $parentCategory->{'title:ar'}         = 'عقارات اسطنبول';
        $parentCategory->{'title:en'}         = 'real_estate';
        $parentCategory->{'description:ar'}   = '';
        $parentCategory->{'description:en'}   = '';
        $parentCategory->{'brief:ar'}         = '';
        $parentCategory->{'brief:en'}         = '';
        $parentCategory->save();

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع في اسطنبول إطلالة بحر')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع في اسطنبول إطلالة بحر';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id , $seaViewCategory->id ,$ApartmentsCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع أقساط في اسطنبول ')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع أقساط في اسطنبول ';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id ,$ApartmentsCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع  في اسطنبول رخيصة ')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع  في اسطنبول رخيصة ';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id ,$ApartmentsCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('فلل للبيع في اسطنبول')
        ]);
        $filter->{'title:ar'}         =  'فلل للبيع في اسطنبول';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id ,$VillaCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('أراضي للبيع في اسطنبول')
        ]);
        $filter->{'title:ar'}         =  'أراضي للبيع في اسطنبول';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id  ,$landsCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('مزارع للبيع في اسطنبول')
        ]);
        $filter->{'title:ar'}         =  'مزارع للبيع في اسطنبول';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id , $FarmsCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('محلات للبيع في اسطنبول')
        ]);
        $filter->{'title:ar'}         =  'محلات للبيع في اسطنبول';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id  ,$ShopsCategory->id
        ]);

        $parentCategory                       = Category::firstOrNew([
            'type' => 'real_estate',
            'slug' => Str::slug('real_estate')
        ]);
        $parentCategory->parent_id            = null;
        $parentCategory->{'title:ar'}         = 'شقق اسطنبول';
        $parentCategory->{'title:en'}         = 'real_estate';
        $parentCategory->{'description:ar'}   = '';
        $parentCategory->{'description:en'}   = '';
        $parentCategory->{'brief:ar'}         = '';
        $parentCategory->{'brief:en'}         = '';
        $parentCategory->save();

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع في بيليك دوزر')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع في بيليك دوزر';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع في باشاك شهير')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع في باشاك شهير';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع في بهجة شهير')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع في بهجة شهير';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع في باسين اكسبريس')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع في باسين اكسبريس';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع في اسنيورت')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع في اسنيورت';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع في شيشلي')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع في شيشلي';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

        $filter                       = Content::firstOrNew([
            'type' => 'filters',
            'slug' => Str::slug('شقق للبيع في الفاتح')
        ]);
        $filter->{'title:ar'}         =  'شقق للبيع في الفاتح';
        $filter->{'title:en'}         = '';
        $filter->{'description:ar'}   = '';
        $filter->{'description:en'}   = '';
        $filter->{'brief:ar'}         = '';
        $filter->{'brief:en'}         = '';
        $filter->save();
        $filter->categories()->sync([
            $forSaleCategory->id, $parentCategory->id
        ]);

    }
    public function createCurrency(){
        $currency                       = Content::where('currency_code','USD')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('usd')
        ]);
        $currency->{'title:ar'}         = 'usd';
        $currency->{'title:en'}         = 'usd';
        $currency->{'title:tr'}         = 'usd';
        $currency->currency_code        = 'USD';
        $currency->currency_symbol      = '$';
        $currency->slug                 = 'usd';
        $currency->save();

        $currency                       = Content::where('currency_code','TRY')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('try')
        ]);
        $currency->{'title:ar'}         = 'try';
        $currency->{'title:en'}         = 'try';
        $currency->{'title:tr'}         = 'try';
        $currency->currency_code        = 'TRY';
        $currency->currency_symbol      = '₺';
        $currency->slug                 = 'try';
        $currency->save();
        $currency                       = Content::where('currency_code','GBP')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('usd')
        ]);

        $currency->{'title:ar'}         = 'gbp';
        $currency->{'title:en'}         = 'gbp';
        $currency->{'title:tr'}         = 'gbp';
        $currency->currency_code        = 'GBP';
        $currency->currency_symbol      = '$';
        $currency->slug                 = 'gbp';
        $currency->save();

        $currency                       = Content::where('currency_code','CHF')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('try')
        ]);

        $currency->{'title:ar'}         = 'chf';
        $currency->{'title:en'}         = 'chf';
        $currency->{'title:tr'}         = 'chf';
        $currency->currency_code        = 'CHF';
        $currency->currency_symbol      = '₺';
        $currency->slug                 = 'chf';
        $currency->save();
        $currency                       = Content::where('currency_code','CAD')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('usd')
        ]);

        $currency->{'title:ar'}         = 'cad';
        $currency->{'title:en'}         = 'cad';
        $currency->{'title:tr'}         = 'cad';
        $currency->currency_code        = 'CAD';
        $currency->currency_symbol      = '$';
        $currency->slug                 = 'cad';
        $currency->save();

        $currency                       = Content::where('currency_code','AUD')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('try')
        ]);

        $currency->{'title:ar'}         = 'aud';
        $currency->{'title:en'}         = 'aud';
        $currency->{'title:tr'}         = 'aud';
        $currency->currency_code        = 'AUD';
        $currency->currency_symbol      = '₺';
        $currency->slug                 = 'aud';
        $currency->save();

        $currency                       = Content::where('currency_code','CNY')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('try')
        ]);

        $currency->{'title:ar'}         = 'cny';
        $currency->{'title:en'}         = 'cny';
        $currency->{'title:tr'}         = 'cny';
        $currency->currency_code        = 'CNY';
        $currency->currency_symbol      = '₺';
        $currency->slug                 = 'cny';
        $currency->save();
        $currency                       = Content::where('currency_code','RUB')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('try')
        ]);

        $currency->{'title:ar'}         = 'rub';
        $currency->{'title:en'}         = 'rub';
        $currency->{'title:tr'}         = 'rub';
        $currency->currency_code        = 'RUB';
        $currency->currency_symbol      = '₺';
        $currency->slug                 = 'rub';
        $currency->save();

        $currency                       = Content::where('currency_code','D')->firstOrNew([
            'type' => 'currencies',
            // 'slug' => Str::slug('try')
        ]);

        $currency->{'title:ar'}         = 'dry';
        $currency->{'title:en'}         = 'dry';
        $currency->{'title:tr'}         = 'dry';
        $currency->currency_code        = 'DRY';
        $currency->currency_symbol      = '₺';
        $currency->slug                 = 'dry';
        $currency->save();
    }
    public function createAgent(){
        $agent                       = Content::firstOrNew([
            'type' => 'agents',
            'slug' => Str::slug('محمد الأحمد')
        ]);
        $agent->{'title:ar'}         = 'محمد الأحمد';
        $agent->{'title:en'}         = 'Muh Ahmad';
        $agent->{'description:ar'}   = '';
        $agent->{'description:en'}   = '';
        $agent->{'brief:ar'}         = '';
        $agent->{'brief:en'}         = '';
        $agent->save();

        $agent                       = Content::firstOrNew([
            'type' => 'agents',
            'slug' => Str::slug('علي الخليل')
        ]);
        $agent->{'title:ar'}         = 'علي الخليل';
        $agent->{'title:en'}         = 'Ali alkhalil';
        $agent->{'description:ar'}   = '';
        $agent->{'description:en'}   = '';
        $agent->{'brief:ar'}         = '';
        $agent->{'brief:en'}         = '';
        $agent->save();

        $agent                       = Content::firstOrNew([
            'type' => 'agents',
            'slug' => Str::slug('عبد الرحمن الخالدي')
        ]);
        $agent->{'title:ar'}         = 'عبد الرحمن الخالدي';
        $agent->{'title:en'}         = 'Abd alkhalil';
        $agent->{'description:ar'}   = '';
        $agent->{'description:en'}   = '';
        $agent->{'brief:ar'}         = '';
        $agent->{'brief:en'}         = '';
        $agent->save();


        $agent                       = Content::firstOrNew([
            'type' => 'agents'
        ]);
        $agent->{'title:ar'}         = 'مالك الحسن';
        $agent->{'title:en'}         = 'Malik alhassan';
        $agent->{'description:ar'}   = '';
        $agent->{'description:en'}   = '';
        $agent->{'brief:ar'}         = '';
        $agent->{'brief:en'}         = '';
        $agent->save();


    }
    public function createOffice(){
        $agent                       = Content::firstOrNew([
            'type' => 'offices'
        ]);
        $agent->{'title:ar'}         = 'سفرك';
        $agent->{'title:en'}         = 'Safaraq';
        $agent->{'description:ar'}   = '';
        $agent->{'description:en'}   = '';
        $agent->{'brief:ar'}         = '';
        $agent->{'brief:en'}         = '';
        $agent->save();

        $agent                       = Content::firstOrNew([
            'type' => 'offices'
        ]);
        $agent->{'title:ar'}         = ' علاجك الطبية ';
        $agent->{'title:en'}         = 'Medical';
        $agent->{'description:ar'}   = '';
        $agent->{'description:en'}   = '';
        $agent->{'brief:ar'}         = '';
        $agent->{'brief:en'}         = '';
        $agent->save();

        $agent                       = Content::firstOrNew([
            'type' => 'offices'
        ]);
        $agent->{'title:ar'}         = 'IMT';
        $agent->{'title:en'}         = 'IMT';
        $agent->{'description:ar'}   = '';
        $agent->{'description:en'}   = '';
        $agent->{'brief:ar'}         = '';
        $agent->{'brief:en'}         = '';
        $agent->save();


        $agent                       = Content::firstOrNew([
            'type' => 'offices'
        ]);
        $agent->{'title:ar'}         = 'Aldirasa';
        $agent->{'title:en'}         = 'Aldirasa';
        $agent->{'description:ar'}   = '';
        $agent->{'description:en'}   = '';
        $agent->{'brief:ar'}         = '';
        $agent->{'brief:en'}         = '';
        $agent->save();


    }
    public function createConfig(){
        $config                       = Config::where('key', 'email')->firstOrNew([
            'val' => 'someone@example.com'
        ]);
        $config->key            = 'email';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'الإيميل';
        $config->{'label:en'}   = 'Email';
        $config->{'title:ar'}   = 'الإيميل';
        $config->{'title:en'}   = 'Email';
        $config->save();

        $config                       = Config::where('key', 'footer_about_company')->firstOrNew([
            'val' => 'نص عن الشركة'
        ]);
        $config->key            = 'footer_about_company';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'حول الشركة (فوتر)';
        $config->{'label:en'}   = 'About Company (Footer)';
        $config->{'title:ar'}   = 'حول الشركة (فوتر)';
        $config->{'title:en'}   = 'About Company (Footer)';
        $config->save();


        $config                       = Config::where('key', 'mobile_number_1')->firstOrNew([
            'val' => '00905365894444'
        ]);
        $config->key            = 'mobile_number_1';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'رقم الهاتف (1)';
        $config->{'label:en'}   = 'Mobile Number 1';
        $config->{'title:ar'}   = 'رقم الهاتف (1)';
        $config->{'title:en'}   = 'Mobile Number 1';
        $config->save();


        $config                       = Config::where('key', 'mobile_number_2')->firstOrNew([
            'val' => '00905365893333'
        ]);
        $config->key            = 'mobile_number_2';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'رقم الهاتف (2)';
        $config->{'label:en'}   = 'Mobile Number 2';
        $config->{'title:ar'}   = 'رقم الهاتف (2)';
        $config->{'title:en'}   = 'Mobile Number 2';
        $config->save();

        $config                       = Config::where('key', 'mobile_number_3')->firstOrNew([
            'val' => '009053658929999'
        ]);
        $config->key            = 'mobile_number_3';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'رقم الهاتف (3)';
        $config->{'label:en'}   = 'Mobile Number 3';
        $config->{'title:ar'}   = 'رقم الهاتف (3)';
        $config->{'title:en'}   = 'Mobile Number 3';
        $config->save();

        $config                       = Config::where('key', 'facebook')->firstOrNew([
            'val' => 'http://facebook.com/'
        ]);
        $config->key            = 'facebook';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'Facebook';
        $config->{'label:en'}   = 'Facebook';
        $config->{'title:ar'}   = 'Facebook';
        $config->{'title:en'}   = 'Facebook';
        $config->save();

        $config                       = Config::where('key', 'whatsapp')->firstOrNew([
            'val' => 'https://www.whatsapp.com/'
        ]);
        $config->key            = 'whatsapp';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'whatsapp';
        $config->{'label:en'}   = 'whatsapp';
        $config->{'title:ar'}   = 'whatsapp';
        $config->{'title:en'}   = 'whatsapp';
        $config->save();


        $config                       = Config::where('key', 'twitter')->firstOrNew([
            'val' => 'https://twitter.com/home'
        ]);
        $config->key            = 'twitter';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'Twitter';
        $config->{'label:en'}   = 'Twitter';
        $config->{'title:ar'}   = 'Twitter';
        $config->{'title:en'}   = 'Twitter';
        $config->save();

        $config                       = Config::where('key', 'instagram')->firstOrNew([
            'val' => 'https://www.instagram.com/'
        ]);
        $config->key            = 'instagram';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'Instagram';
        $config->{'label:en'}   = 'Instagram';
        $config->{'title:ar'}   = 'Instagram';
        $config->{'title:en'}   = 'Instagram';
        $config->save();

        $config                       = Config::where('key', 'youtube')->firstOrNew([
            'val' => 'https://www.youtube.com/'
        ]);
        $config->key            = 'youtube';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'Youtube';
        $config->{'label:en'}   = 'Youtube';
        $config->{'title:ar'}   = 'Youtube';
        $config->{'title:en'}   = 'Youtube';
        $config->save();

        $config                       = Config::where('key', 'telegram')->firstOrNew([
            'val' => 'https://telegram.org/'
        ]);
        $config->key            = 'telegram';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'Telegram';
        $config->{'label:en'}   = 'Telegram';
        $config->{'title:ar'}   = 'Telegram';
        $config->{'title:en'}   = 'Telegram';
        $config->save();

        $config                       = Config::where('key', 'linkedin')->firstOrNew([
            'val' => 'https://linkedin.org/'
        ]);
        $config->key            = 'linkedin';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'LinkedIn';
        $config->{'label:en'}   = 'LinkedIn';
        $config->{'title:ar'}   = 'LinkedIn';
        $config->{'title:en'}   = 'LinkedIn';
        $config->save();

        $config                       = Config::where('key', 'lang')->firstOrNew([
            'val' => '37.0587509'
        ]);
        $config->key            = 'lang';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'احداثيات خط الطول';
        $config->{'label:en'}   = 'Longitude';
        $config->{'title:ar'}   = 'احداثيات خط الطول';
        $config->{'title:en'}   = 'Longitude';
        $config->save();

        $config                       = Config::where('key', 'lat')->firstOrNew([
            'val' => '37.310096'
        ]);
        $config->key            = 'lat';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'احداثيات خط العرض';
        $config->{'label:en'}   = 'Latitude';
        $config->{'title:ar'}   = 'احداثيات خط العرض';
        $config->{'title:en'}   = 'Latitude';
        $config->save();

        $config                       = Config::where('key', 'address')->firstOrNew([
            'val' => 'https://telegram.org/'
        ]);
        $config->key            = 'address';
        $config->input_type     = 'TEXTAREA';
        $config->{'label:ar'}   = 'العنوان';
        $config->{'label:en'}   = 'Address';
        $config->{'title:ar'}   = 'العنوان';
        $config->{'title:en'}   = 'Address';
        $config->save();
        $config                       = Config::where('key', 'facebook_messenger')->firstOrNew([
            'val' => 'facebook messenger'
        ]);
        $config->key            = 'facebook_messenger';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'facebook messenger';
        $config->{'label:en'}   = 'facebook messenger';
        $config->{'title:ar'}   = 'facebook messenger';
        $config->{'title:en'}   = 'facebook messenger';
        $config->save();
    }
    public function createStats(){
        $config                 = Config::where('key', 'years_of_experience')->firstOrNew([
            'val' => '9'
        ]);
        $config->key            = 'years_of_experience';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'سنوات الخبرة';
        $config->{'label:en'}   = 'Years of Experience';
        $config->{'title:ar'}   = 'سنوات الخبرة';
        $config->{'title:en'}   = 'Years of Experience';
        $config->save();

        $config                 = Config::where('key', 'projects_count')->firstOrNew([
            'val' => '9'
        ]);
        $config->key            = 'projects_count';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'المشاريع';
        $config->{'label:en'}   = 'Projects';
        $config->{'title:ar'}   = 'المشاريع';
        $config->{'title:en'}   = 'Projects';
        $config->save();

        $config                 = Config::where('key', 'project_count')->firstOrNew([
            'val' => '200'
        ]);
        $config->key            = 'project_count';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'المشاريع';
        $config->{'label:en'}   = 'Projects';
        $config->{'title:ar'}   = 'المشاريع';
        $config->{'title:en'}   = 'Projects';
        $config->save();

        $config                 = Config::where('key', 'happy_clients')->firstOrNew([
            'val' => '1200'
        ]);
        $config->key            = 'happy_clients';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'عملاء سعداء';
        $config->{'label:en'}   = 'Happy Clients';
        $config->{'title:ar'}   = 'عملاء سعداء';
        $config->{'title:en'}   = 'Happy Clients';
        $config->save();

        $config                 = Config::where('key', 'sales')->firstOrNew([
            'val' => '120000000'
        ]);
        $config->key            = 'sales';
        $config->input_type     = 'TEXT';
        $config->{'label:ar'}   = 'المبيعات (ليرة تركية)';
        $config->{'label:en'}   = 'Sales (TL)';
        $config->{'title:ar'}   = 'المبيعات (ليرة تركية)';
        $config->{'title:en'}   = 'Sales (TL)';
        $config->save();
    }
}
