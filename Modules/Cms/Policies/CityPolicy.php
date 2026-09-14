<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Cms\Entities\City;
use App\User;

class CityPolicy
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
     * Determine whether the City can view resourses.
     *
     * @param  City|null $city
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('cities.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the City can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('cities.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the City can update resourses.
     *
     * @param  User $auth
     * @param  City $city
     * @return Response
     */
    public function update(User $auth, City $city)
    {
        return $auth->can('cities.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the City can delete resourses.
     *
     * @param  User $auth
     * @param  City $city
     * @return Response
     */
    public function delete(User $auth, City $city)
    {
        return $auth->can('cities.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    /**
     * Determine whether the City can delete translations of resourses.
     *
     * @param  User $auth
     * @param  City $city
     * @return Response
     */
    public function deleteTranslation(User $auth, City $city)
    {
        return $auth->can('cities.delete_translation')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function disable(User $auth, City $city)
    {
        return $auth->can('cities.disable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function enable(User $auth, City $city)
    {
        return $auth->can('cities.enable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the City can restore resourses.
     *
     * @param  User $auth
     * @param  City $city
     * @return Response
     */
    public function restore(User $auth, City $city)
    {
        return $auth->can('cities.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the City can delete resourses permanently.
     *
     * @param  User $auth
     * @param  City $city
     * @return Response
     */
    public function forceDelete(User $auth, City $city)
    {
        return $auth->can('cities.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the City can delete resourses permanently.
     *
     * @param  User $auth
     * @param  City $city
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('cities.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
