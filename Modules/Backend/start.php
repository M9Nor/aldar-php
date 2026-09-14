<?php

use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\Content;
use Modules\Cms\Entities\LandingPage;

use Modules\Backend\Entities\Project;
use Modules\Permissions\Entities\Ability;
use Modules\Permissions\Entities\AbilityGroup;
use Modules\Permissions\Entities\Role;
/*
|--------------------------------------------------------------------------
| Register Namespaces And Routes
|--------------------------------------------------------------------------
|
| When a module starting, this file will executed automatically. This helps
| to register some namespaces like translator or view. Also this file
| will load the routes file for each module. You may also modify
| this file as you want.
|
*/

view()->composer('cms::includes.aside', function($view) {
    // Oroject Information
    $projectItems = [];
    if(auth()->user()->can('view', Project::class))
    {
        $projectItems[] = [
            'label'     => __('cms::includes.aside.show_all'),
            'link'      => route('ProjectController@index'),
        ];
    }
    if(auth()->user()->can('create', Project::class))
    {
        $projectItems[] = [
            'label'     => __('cms::includes.aside.create'),
            'link'      => route('ProjectController@create'),
        ];
    }
    // project categories
    // dd(auth()->user()->can('view',[ Category::class,'contracts']));
    if(Category::getTypeInfo('contracts')['aside_menu'] && auth()->user()->can('view', [Category::class,'contracts']))
    {
        $projectItems[] = [
            'label'         => Category::getTypeTitle('contracts'),
            'link'          => route('CategoryController@index', ['type' => 'contracts']),
        ];
    }

    if(Category::getTypeInfo('property_classifications')['aside_menu'] && auth()->user()->can('view',[ Category::class,'property_classifications']))
    {
        $projectItems[] = [
            'label'         => Category::getTypeTitle('property_classifications'),
            'link'          => route('CategoryController@index', ['type' => 'property_classifications']),
        ];
    }
    if(Category::getTypeInfo('property_status')['aside_menu'] && auth()->user()->can('view',[ Category::class,'property_status']))
    {
        $projectItems[] = [
            'label'         => Category::getTypeTitle('property_status'),
            'link'          => route('CategoryController@index', ['type' => 'property_status']),
        ];
    }
    if(Category::getTypeInfo('property_features')['aside_menu'] && auth()->user()->can('view',[ Category::class,'property_features']))
    {
        $projectItems[] = [
            'label'         => Category::getTypeTitle('property_features'),
            'link'          => route('CategoryController@index', ['type' => 'property_features']),
        ];
    }
    if(Category::getTypeInfo('facilities')['aside_menu'] && auth()->user()->can('view',[ Category::class,'facilities']))
    {
        $projectItems[] = [
            'label'         => Category::getTypeTitle('facilities'),
            'link'          => route('CategoryController@index', ['type' => 'facilities']),
        ];
    }
    if(Category::getTypeInfo('payments')['aside_menu'] && auth()->user()->can('view',[ Category::class,'payments']))
    {
        $projectItems[] = [
            'label'         => Category::getTypeTitle('payments'),
            'link'          => route('CategoryController@index', ['type' => 'payments']),
        ];
    }
    // project contents
    if(Content::getTypeInfo('agents') && auth()->user()->can('view', [Content::class,'agents']))
    {
        $projectItems[] = [
            'label'         => Content::getTypeTitle('agents'),
            'link'      => route('ContentController@index', ['type' => 'agents']),
        ];
    }
    if(Category::getTypeInfo('agents') && auth()->user()->can('view',[ Category::class,'agents'])){
        $projectItems[] = [
            'label'     => __('cms::cruds.agents_categories'),
            'link'      => route('CategoryController@index',['type' => 'agents']),
        ];
    }
    if(Content::getTypeInfo('currencies') && auth()->user()->can('view', [Content::class,'currencies']))
    {
        $projectItems[] = [
            'label'         => Content::getTypeTitle('currencies'),
            'link'      => route('ContentController@index', ['type' => 'currencies']),
        ];
    }
    if(!empty($projectItems))
    {
        if
        (
            ( auth()->user()->can('view', Project::class) ) ||
            ( auth()->user()->can('create', Project::class) ) ||
            ( Category::getTypeInfo('contracts')['aside_menu'] && auth()->user()->can('view',[ Category::class,'contracts']) ) ||
            ( Category::getTypeInfo('property_classifications')['aside_menu'] && auth()->user()->can('view',[ Category::class,'property_classifications']) ) ||
            ( Category::getTypeInfo('property_status')['aside_menu'] && auth()->user()->can('view',[ Category::class,'property_status']) ) ||
            ( Category::getTypeInfo('property_features')['aside_menu'] && auth()->user()->can('view',[ Category::class,'property_features']) ) ||
            ( Category::getTypeInfo('facilities')['aside_menu'] && auth()->user()->can('view',[ Category::class,'facilities']) ) ||
            ( Category::getTypeInfo('payments')['aside_menu'] && auth()->user()->can('view',[ Category::class,'payments']) ) ||
            ( Content::getTypeInfo('agents') && auth()->user()->can('view', [Content::class,'agents']) ) ||
            ( Content::getTypeInfo('currencies') && auth()->user()->can('view', [Content::class,'currencies']) )
        )
        {
            app()->make('Cms')->asideMenu([
                'header'    => __('backend::projects.title'),
                'label'     => __('backend::projects.title'),
                'icon'      => config('cms.svgs.projects'),
                'items'     => $projectItems,
                'ordering'  => 0
            ]);
        }
    }

  if(auth()->user()->can('view', Project::class))
    {
        $projectItems[] = [
            'label'     => __('cms::includes.aside.show_all'),
            'link'      => route('ProjectController@index'),
        ];
    }
    if(auth()->user()->can('create', Project::class))
    {
        $projectItems[] = [
            'label'     => __('cms::includes.aside.create'),
            'link'      => route('ProjectController@create'),
        ];
    }
    
    $opportunityPageItems = [];

       if(auth()->user()->can('view', Project::class))
    {
        $opportunityPageItems[] = [
            'label'     => __('cms::includes.aside.show_all'),
            'link'      => route('OpportunityController@index'),
        ];
    }
    if(auth()->user()->can('create', Project::class))
    {
        $opportunityPageItems[] = [
            'label'     => __('cms::includes.aside.create'),
            'link'      => route('OpportunityController@create'),
        ];
    }

    if(Category::getTypeInfo('opportunity_classifications')['aside_menu'] && auth()->user()->can('view',[ Category::class,'opportunity_classifications']))
    {
        $opportunityPageItems[] = [
            'label'         => Category::getTypeTitle('opportunity_classifications'),
            'link'          => route('CategoryController@index', ['type' => 'opportunity_classifications']),
        ];
    }

    if(auth()->user()->can('view', Project::class) || auth()->user()->can('create', Project::class) || Category::getTypeInfo('opportunity_classifications')['aside_menu'] && auth()->user()->can('view',[ Category::class,'opportunity_classifications'])){
        app()->make('Cms')->asideMenu([
            // 'header'    => __('backend::contents.channel_blog'),
            'label'     => __('cms::includes.investment_opportunities'),
            'icon'      => config('cms.svgs.projects'),
            'items'     => $opportunityPageItems,
            'ordering'  => 2
        ]);
    }
    

    $landingPageItems = [];
    if(auth()->user()->can('view', LandingPage::class))
    {
        $landingPageItems[] = [
            'label'     => __('cms::landing_page.show_all'),
            'link'      => route('LandingPageController@index'),
        ];
    }
    if(auth()->user()->can('create', LandingPage::class))
    {
        $landingPageItems[] = [
            'label'     => __('cms::landing_page.create'),
            'link'      => route('LandingPageController@create'),
        ];
    }
    if(auth()->user()->can('view', LandingPage::class) || auth()->user()->can('create', LandingPage::class)){
        app()->make('Cms')->asideMenu([
            // 'header'    => __('backend::contents.channel_blog'),
            'label'     => 'Landing Pages',
            'icon'      => config('cms.svgs.articles'),
            'items'     => $landingPageItems,
            'ordering'  => 5
        ]);
    }
    
    // channel section
    if(Content::getTypeInfo('articles') && auth()->user()->can('view', [Content::class,'articles'])){
        $articlesItems[] = [
            'label'     => Content::getTypeTitle('articles'),
            'link'      => route('ContentController@index',['type' => 'articles']),
        ];
    }
    if(Category::getTypeInfo('articles') && auth()->user()->can('view',[ Category::class,'articles'])){
        $articlesItems[] = [
            'label'     => __('cms::cruds.categories.categories'),
            'link'      => route('CategoryController@index',['type' => 'articles']),
        ];
    }
    if( (Category::getTypeInfo('articles') && auth()->user()->can('view',[ Category::class,'articles'])) || (Content::getTypeInfo('articles') && auth()->user()->can('view', [content::class,'articles'])))
    {
        app()->make('Cms')->asideMenu([
            'header'    => __('backend::contents.channel_blog'),
            'label'     => Content::getTypeTitle('articles'),
            'icon'      => config('cms.svgs.articles'),
            'items'     => $articlesItems,
            'ordering'  => 5
        ]);
    }
    // if(Content::getTypeInfo('news') && auth()->user()->can('view', [Content::class,'news'])){
    //     $newsItems[] = [
    //         'label'     => Content::getTypeTitle('news'),
    //         'link'      => route('ContentController@index',['type' => 'news']),
    //     ];
    // }
    // if(Category::getTypeInfo('news') && auth()->user()->can('view',[ Category::class,'news'])){
    //     $newsItems[] = [
    //         'label'     => __('cms::cruds.categories.categories'),
    //         'link'      => route('CategoryController@index',['type' => 'news']),
    //     ];
    // }
    // if( (Category::getTypeInfo('news') && auth()->user()->can('view',[ Category::class,'news'])) || (Content::getTypeInfo('news') && auth()->user()->can('view', [content::class,'news'])))
    // {
    //     app()->make('Cms')->asideMenu([
    //         'label'     => Content::getTypeTitle('news'),
    //         'icon'      => config('cms.svgs.news'),
    //         'items'     => $newsItems,
    //         'ordering'  => 5
    //     ]);
    // }
    if(Content::getTypeInfo('playlist_videos') && auth()->user()->can('view', [Content::class,'playlist_videos'])){
        $playListItems[] = [
            'label'     => Content::getTypeTitle('playlist_videos'),
            'link'      => route('ContentController@index',['type' => 'playlist_videos']),
        ];
    }
    // if(Category::getTypeInfo('playlist_videos') && auth()->user()->can('view',[ Category::class,'playlist_videos'])){
    //     $playListItems[] = [
    //         'label'     => Category::getTypeTitle('playlist_videos'),
    //         'link'      => route('CategoryController@index',['type' => 'playlist_videos']),
    //     ];
    // }
    if( (Category::getTypeInfo('playlist_videos') && auth()->user()->can('view',[ Category::class,'playlist_videos'])) || (Content::getTypeInfo('playlist_videos') && auth()->user()->can('view', [content::class,'playlist_videos'])))
    {
        app()->make('Cms')->asideMenu([
            'label'     => __('backend::contents.channel'),
            'icon'      => config('cms.svgs.playlist_videos'),
            'items'     => $playListItems,
            'ordering'  => 5
        ]);
    }
    if(Content::getTypeInfo('faqs') && auth()->user()->can('view', [Content::class,'faqs'])){
        $faqsItems[] = [
            'label'     => Content::getTypeTitle('faqs'),
            'link'      => route('ContentController@index',['type' => 'faqs']),
        ];
    }
    if(Category::getTypeInfo('faqs') && auth()->user()->can('view',[ Category::class,'faqs'])){
        $faqsItems[] = [
            'label'     => __('cms::cruds.categories.categories'),
            'link'      => route('CategoryController@index',['type' => 'faqs']),
        ];
    }
    if( (Category::getTypeInfo('faqs') && auth()->user()->can('view',[ Category::class,'faqs'])) || (Content::getTypeInfo('faqs') && auth()->user()->can('view', [content::class,'faqs'])))
    {
        app()->make('Cms')->asideMenu([
            'label'     => Content::getTypeTitle('faqs'),
            'icon'      => config('cms.svgs.faqs'),
            'items'     => $faqsItems,
            'ordering'  => 5
        ]);
    }
    // Content Section
    // if(Content::getTypeInfo('advertisements') && auth()->user()->can('view', [Content::class,'advertisements'])){
    //     app()->make('Cms')->asideMenu([
    //         'header'    => __('cms::cruds.contents.contents'),
    //         'label'     => Content::getTypeTitle('advertisements'),
    //         'link'      => route('ContentController@index', ['type' => 'advertisements']),
    //         'icon'      => config('cms.svgs.advertisements'),
    //         'ordering'  => 100
    //     ]);
    // }
    if(Content::getTypeInfo('filters') && auth()->user()->can('view', [Content::class,'filters'])){
        // $permissionGroups = collect([]);

        // // Retrieve super administrator role to assign the seeded abilities.
        // $superAdminRole = Role::firstOrNew([
        //     'name' => 'SUPERADMIN'
        // ]);
        // $type = 'filters';
        // $singular = Str::singular($type);
        // $group                  = AbilityGroup::firstOrNew([
        //     'name' => strtoupper($singular).'_CATS_MANAGEMENT'
        // ]);
        // $group->icon            = 'fa fa-fw fa-lg fa-sitemap';
        // $group->{'title:ar'}    = 'إدارة التصنيفات ['.Category::getTypeTitle($type).']';
        // $group->{'title:en'}    = 'Categories ['.Str::title($singular).' Management]';
        // $group->save();

        // $permissionGroups->push([
        //     'group'       => $group,
        //     'permissions' => [
        //         'categories.'.strtolower($type).'.view' => [
        //             'ar' => [
        //                 'title' => 'عرض',
        //             ],
        //             'en' => [
        //                 'title' => 'View',
        //             ]
        //         ],
        //         'categories.'.strtolower($type).'.create' => [
        //             'ar' => [
        //                 'title' => 'إضافة',
        //             ],
        //             'en' => [
        //                 'title' => 'Create',
        //             ]
        //         ],
        //         'categories.'.strtolower($type).'.edit' => [
        //             'ar' => [
        //                 'title' => 'تعديل',
        //             ],
        //             'en' => [
        //                 'title' => 'Edit',
        //             ]
        //         ],
        //         'categories.'.strtolower($type).'.delete' => [
        //             'ar' => [
        //                 'title' => 'حذف',
        //             ],
        //             'en' => [
        //                 'title' => 'Delete',
        //             ]
        //         ],
        //         'categories.'.strtolower($type).'.delete_translation' => [
        //             'ar' => [
        //                 'title' => 'حذف الترجمة',
        //             ],
        //             'en' => [
        //                 'title' => 'Delete a Translation',
        //             ]
        //         ],
        //         'categories.'.strtolower($type).'.view_delete' => [
        //             'ar' => [
        //                 'title' => 'عرض المحذوفات',
        //             ],
        //             'en' => [
        //                 'title' => 'View Deleted Items',
        //             ]
        //         ],
        //         'categories.'.strtolower($type).'.restore' => [
        //             'ar' => [
        //                 'title' => 'استعادة',
        //             ],
        //             'en' => [
        //                 'title' => 'Restore',
        //             ]
        //         ],
        //         'categories.'.strtolower($type).'.force_delete' => [
        //             'ar' => [
        //                 'title' => 'حذف نهائي',
        //             ],
        //             'en' => [
        //                 'title' => 'Delete Permanently',
        //             ]
        //         ],
        //     ]
        // ]);
        // foreach($permissionGroups as $group)
        // {
        //     foreach($group['permissions'] as $code => $translations)
        //     {
        //         $permission = $group['group']->abilities()->updateOrCreate([
        //             'name' => $code
        //         ]);

        //         $permission->fill($translations);
        //         $permission->save();

        //         $superAdminRole->allow($code);
        //     }

        // }
        // // Refresh the permissions cache.
        // Bouncer::refresh();
        // app()->make('Cms')->asideMenu([
        //     'label'     => Content::getTypeTitle('filters'),
        //     'link'      => route('ContentController@index', ['type' => 'filters']),
        //     'icon'      => config('cms.svgs.filters'),
        //     'ordering'  => 100
        // ]);
    }
    if(Content::getTypeInfo('filters') && auth()->user()->can('view', [Content::class,'filters'])){
        $filtersItems[] = [
            'label'     => Content::getTypeTitle('filters'),
            'link'      => route('ContentController@index',['type' => 'filters']),
        ];
    }
    if(Category::getTypeInfo('filters') && auth()->user()->can('view',[ Category::class,'filters'])){
        $filtersItems[] = [
            'label'     => __('cms::cruds.categories.categories'),
            'link'      => route('CategoryController@index',['type' => 'filters']),
        ];
    }
    if(!empty($filtersItems))
    {
        app()->make('Cms')->asideMenu([
            'label'     => Content::getTypeTitle('filters'),
            'icon'      => config('cms.svgs.filters'),
            'items'     => $filtersItems,
            'ordering'  => 5
        ]);
    }
    // if(Content::getTypeInfo('sliders') && auth()->user()->can('view', [Content::class,'sliders'])){
    //     app()->make('Cms')->asideMenu([
    //         'label'     => Content::getTypeTitle('sliders'),
    //         'link'      => route('ContentController@index', ['type' => 'sliders']),
    //         'icon'      => config('cms.svgs.sliders'),
    //         'ordering'  => 100
    //     ]);
    // }
    if(Content::getTypeInfo('stories') && auth()->user()->can('view', [Content::class,'stories'])){
        app()->make('Cms')->asideMenu([
            'label'     => Content::getTypeTitle('stories'),
            'link'      => route('ContentController@index', ['type' => 'stories']),
            'icon'      => config('cms.svgs.stories'),
            'ordering'  => 100
        ]);
    }
    if(Content::getTypeInfo('testimonials') && auth()->user()->can('view', [Content::class,'testimonials'])){
        app()->make('Cms')->asideMenu([
            'label'     => Content::getTypeTitle('testimonials'),
            'link'      => route('ContentController@index', ['type' => 'testimonials']),
            'icon'      => config('cms.svgs.testimonials'),
            'ordering'  => 100
        ]);
    }
    if(Content::getTypeInfo('services') && auth()->user()->can('view', [Content::class,'services'])){
        app()->make('Cms')->asideMenu([
            'label'     => Content::getTypeTitle('services'),
            'link'      => route('ContentController@index', ['type' => 'services']),
            'icon'      => config('cms.svgs.services'),
            'ordering'  => 100
        ]);
    }
    // if(Content::getTypeInfo('achievements') && auth()->user()->can('view', [Content::class,'achievements'])){
    //     app()->make('Cms')->asideMenu([
    //         'label'     => Content::getTypeTitle('achievements'),
    //         'link'      => route('ContentController@index', ['type' => 'achievements']),
    //         'icon'      => config('cms.svgs.services'),
    //         'ordering'  => 100
    //     ]);
    // }
    $bannerItems =[];
    if(Content::getTypeInfo('first_banners') && auth()->user()->can('view', [Content::class,'first_banners'])){
        $bannerItems[] = [
            'label'     => Content::getTypeTitle('first_banners'),
            'link'      => route('ContentController@index', ['type' => 'first_banners']),
        ];
    }
    // if(Content::getTypeInfo('second_banners') && auth()->user()->can('view', [Content::class,'second_banners'])){
    //     $bannerItems[] = [
    //         'label'     =>  Content::getTypeTitle('second_banners'),
    //         'link'      => route('ContentController@index', ['type' => 'second_banners']),
    //     ];
    // }
    if(!empty($bannerItems))
    {
        if( ( Content::getTypeInfo('first_banners') && auth()->user()->can('view', [Content::class,'first_banners']) ) || ( Content::getTypeInfo('second_banners') && auth()->user()->can('view', [Content::class,'second_banners']) ) )
        {
            app()->make('Cms')->asideMenu([
                'label'     => __('backend::cruds.contents.banners.plural'),
                'icon'      => config('cms.svgs.banners'),
                'items'     => $bannerItems,
                'ordering'  => 10
            ]);
        }
    }
    // if(Content::getTypeInfo('offices') && auth()->user()->can('view', [Content::class,'offices'])){
    //     app()->make('Cms')->asideMenu([
    //         'label'     => Content::getTypeTitle('offices'),
    //         'link'      => route('ContentController@index', ['type' => 'offices']),
    //         'icon'      => config('cms.svgs.offices'),
    //         'ordering'  => 100
    //     ]);
    // }
    if(Content::getTypeInfo('pages') && auth()->user()->can('view', [Content::class,'pages'])){
        app()->make('Cms')->asideMenu([
            'label'     => Content::getTypeTitle('pages'),
            'link'      => route('ContentController@index', ['type' => 'pages']),
            'icon'      => config('cms.svgs.pages'),
            'ordering'  => 100
        ]);
    }
    if(auth()->user()->can('requests', \Modules\Cms\Entities\Tag::class)){
        app()->make('Cms')->asideMenu([
            'header'    => __('cms::includes.aside.preferences'),
            'label'     => __('cms::includes.aside.requests'),
            'link'      => route('ProjectController@requests'),
            'icon'      => config('cms.svgs.requests'),
            'ordering'  => 1
        ]);
    }
    if(auth()->user()->can('requests', \Modules\Cms\Entities\Tag::class)){
        // app()->make('Cms')->asideMenu([
        //     'label'     => __('cms::includes.aside.properties'),
        //     'link'      => route('ProjectController@properties'),
        //     'icon'      => config('cms.svgs.properties'),
        //     'ordering'  => 1
        // ]);
    }
    if(auth()->user()->can('view', \Modules\Cms\Entities\Tag::class)){
        app()->make('Cms')->asideMenu([
            'label'     => __('cms::includes.aside.tags'),
            'link'      => route('TagController@index'),
            'icon'      => config('cms.svgs.tags'),
            'ordering'  => 1
        ]);
    }

    $citiesItems = [];
    if(auth()->user()->can('view', \Modules\Cms\Entities\Country::class))
    {
        $citiesItems[] = [
            'label'     => __('cms::areas.countries'),
            'link'      => route('CountryController@index'),
        ];
    }
    if(auth()->user()->can('view', \Modules\Cms\Entities\City::class))
    {
        $citiesItems[] = [
            'label'     => __('cms::areas.cities'),
            'link'      => route('CityController@index'),
        ];
    }
    if(auth()->user()->can('view', \Modules\Cms\Entities\Area::class))
    {
        $citiesItems[] = [
            'label'     => __('cms::areas.title'),
            'link'      => route('AreaController@index'),
        ];
    }
    if(!empty($citiesItems))
    {
        if(auth()->user()->can('view', \Modules\Cms\Entities\Country::class) || auth()->user()->can('view', \Modules\Cms\Entities\City::class) || auth()->user()->can('view', \Modules\Cms\Entities\Area::class))
        {
            app()->make('Cms')->asideMenu([
                'label'     => __('cms::areas.cities_areas'),
                'icon'      => config('cms.svgs.areas'),
                'items'     => $citiesItems,
                'ordering'  => 1
            ]);
        }
    }
    if(auth()->user()->can('view', Modules\Cms\Entities\Config::class)){
        app()->make('Cms')->asideMenu([
            'label'     => __('cms::includes.aside.settings'),
            'link'      => route('ConfigController@index'),
            'icon'      => config('cms.svgs.settings'),
            'ordering'  => 1
        ]);
    }
    if(auth()->user()->can('requests', \Modules\Cms\Entities\Tag::class)){
        app()->make('Cms')->asideMenu([

            'label'     => __('cms::includes.aside.clear_cache'),
            'link'      => route('cache.clear'),
            'icon'      => '
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M11.7071032,12.7071045 C11.3165789,13.0976288 10.6834139,13.0976288 10.2928896,12.7071045 C9.90236532,12.3165802 9.90236532,11.6834152 10.2928896,11.2928909 L16.2928896,5.29289093 C16.6714686,4.914312 17.281055,4.90106637 17.675721,5.26284357 L23.675721,10.7628436 C24.08284,11.136036 24.1103429,11.7686034 23.7371505,12.1757223 C23.3639581,12.5828413 22.7313908,12.6103443 22.3242718,12.2371519 L17.0300721,7.38413553 L11.7071032,12.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.999999, 8.999997) scale(1, -1) rotate(90.000000) translate(-16.999999, -8.999997) "/>
                            <path d="M15.5,8 C16.0522847,8 16.5,8.44771525 16.5,9 C16.5,9.55228475 16.0522847,10 15.5,10 L9,10 C8.44771525,10 8,10.4477153 8,11 L8,21.0415946 C8,21.5938793 7.55228475,22.0415946 7,22.0415946 C6.44771525,22.0415946 6,21.5938793 6,21.0415946 L6,11 C6,9.34314575 7.34314575,8 9,8 L15.5,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        </g>
                    </svg>',
            'ordering'  => 99999999
        ]);
    }
});
