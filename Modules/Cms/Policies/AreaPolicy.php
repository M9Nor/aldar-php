<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Cms\Entities\Area;
use App\User;

class AreaPolicy
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
     * Determine whether the area can view resourses.
     *
     * @param  Area|null $area
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('areas.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the area can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('areas.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the area can update resourses.
     *
     * @param  User $auth
     * @param  Area $area
     * @return Response
     */
    public function update(User $auth, Area $area)
    {
        return $auth->can('areas.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the area can delete resourses.
     *
     * @param  User $auth
     * @param  Area $area
     * @return Response
     */
    public function delete(User $auth, Area $area)
    {
        return $auth->can('areas.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    /**
     * Determine whether the area can delete translations of resourses.
     *
     * @param  User $auth
     * @param  Area $area
     * @return Response
     */
    public function deleteTranslation(User $auth, Area $area)
    {
        return $auth->can('areas.delete_translation')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function disable(User $auth, Area $area)
    {
        return $auth->can('areas.disable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function enable(User $auth, Area $area)
    {
        return $auth->can('areas.enable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the area can restore resourses.
     *
     * @param  User $auth
     * @param  Area $area
     * @return Response
     */
    public function restore(User $auth, Area $area)
    {
        return $auth->can('areas.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the area can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Area $area
     * @return Response
     */
    public function forceDelete(User $auth, Area $area)
    {
        return $auth->can('areas.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the area can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Area $area
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('areas.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
