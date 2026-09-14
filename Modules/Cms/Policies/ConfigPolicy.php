<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Cms\Entities\Config;
use App\User;

class ConfigPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the Config can view resourses.
     *
     * @param  Config|null $config
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('configs.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Config can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('configs.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Config can update resourses.
     *
     * @param  User $auth
     * @param  Config $config
     * @return Response
     */
    public function update(User $auth, Config $config)
    {
        return $auth->can('configs.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Config can delete resourses.
     *
     * @param  User $auth
     * @param  Config $config
     * @return Response
     */
    public function delete(User $auth, Config $config)
    {
        return $auth->can('configs.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Config can restore resourses.
     *
     * @param  User $auth
     * @param  Config $config
     * @return Response
     */
    public function restore(User $auth, Config $config)
    {
        return $auth->can('configs.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Config can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Config $config
     * @return Response
     */
    public function forceDelete(User $auth, Config $config)
    {
        return $auth->can('configs.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Config can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Config $config
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('configs.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
