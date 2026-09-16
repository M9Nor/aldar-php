<?php

namespace Modules\Permissions\Providers;

use Illuminate\Support\ServiceProvider;
use Bouncer;
use Gate;

class BouncerServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->supremeAdmin();
        $this->registerTables();
        $this->registerCustomPermissionModels();
        $this->runBeforePolicies();
    }

    /**
     * Check Bouncer abilities before policies, as Bouncer 1.0.0-rc.8 did on Laravel 7.
     * Bouncer 1.0 moved its gate check to the "after" slot by default.
     *
     * @return void
     */
    protected function runBeforePolicies()
    {
        Bouncer::runBeforePolicies();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Implicitly grant "ROOT" role all permissions.
     *
     * @return void
     */
    public function supremeAdmin()
    {
        // Checks if a specific role is allowed to have full privileges
        if(env('ENABLE_SUPERPOWERS', true)) Gate::before(function ($user, $ability) {
            if($user->isAn('ROOT'))
            {
                return true;
            }

            // Returning false here would result in the policies classes not being checked for non ROOT users.
            return null;
        });
    }

    /**
     * Register tables.
     *
     * @return void
     */
    public function registerTables()
    {
        Bouncer::tables([
            'groups'                => 'perms_groups',
            'group_translations'    => 'perms_group_translations',
            'abilities'             => 'perms_abilities',
            'ability_translations'  => 'perms_ability_translations',
            'roles'                 => 'perms_roles',
            'role_translations'     => 'perms_role_translations',
            'assigned_roles'        => 'perms_assigned_roles',
            'permissions'           => 'perms_permissions',
            'manageables'           => 'perms_manageables'
        ]);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerCustomPermissionModels()
    {
        \Bouncer::useAbilityModel(\Modules\Permissions\Entities\Ability::class);
        \Bouncer::useRoleModel(\Modules\Permissions\Entities\Role::class);
    }
}
