<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\User;

class UserPolicy
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
     * @param  User|null $user
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('users.view')
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
        return $auth->can('users.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can update resourses.
     *
     * @param  User $auth
     * @param  User $user
     * @return Response
     */
    public function update(User $auth, User $user)
    {
        return $auth->can('users.edit') || $auth->id === $user->id
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can delete resourses.
     *
     * @param  User $auth
     * @param  User $user
     * @return Response
     */
    public function delete(User $auth, User $user)
    {
        return $auth->can('users.delete') && $auth->id != $user->id
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can restore resourses.
     *
     * @param  User $auth
     * @param  User $user
     * @return Response
     */
    public function restore(User $auth, User $user)
    {
        return $auth->can('users.restore') && $auth->id != $user->id
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can delete resourses permanently.
     *
     * @param  User $auth
     * @param  User $user
     * @return Response
     */
    public function forceDelete(User $auth, User $user)
    {
        return $auth->can('users.force_delete') && $auth->id !== $user->id
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can delete resourses permanently.
     *
     * @param  User $auth
     * @param  User $user
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('users.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
