<?php

namespace Modules\Permissions\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Permissions\Entities\Role;
use App\User;
use Bouncer;

class RolePolicy
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
     * Determine whether the user can view resourses.
     *
     * @param  Role|null $role
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('roles.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('roles.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can update resourses.
     *
     * @param  User $auth
     * @param  Role $role
     * @return Response
     */
    public function update(User $auth, Role $role)
    {
        return $auth->isA(...$role->roles->pluck('name')) && $auth->can('roles.edit') && (! $auth->isAn($role->name))
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can delete resourses.
     *
     * @param  User $auth
     * @param  Role $role
     * @return Response
     */
    public function delete(User $auth, Role $role)
    {
        return $auth->isA(...$role->roles->pluck('name')) && $auth->can('roles.delete') && (! $auth->isAn($role->name))
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can restore resourses.
     *
     * @param  User $auth
     * @param  Role $role
     * @return Response
     */
    public function restore(User $auth, Role $role)
    {
        return $auth->isA(...$role->roles->pluck('name')) && $auth->can('roles.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Role $role
     * @return Response
     */
    public function forceDelete(User $auth, Role $role)
    {
        return $auth->isA(...$role->roles->pluck('name')) && $auth->can('roles.force_delete') && (! $auth->isAn($role->name))
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Role $role
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('roles.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
