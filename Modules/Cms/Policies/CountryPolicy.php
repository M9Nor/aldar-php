<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Cms\Entities\Country;
use App\User;

class CountryPolicy
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
     * Determine whether the Country can view resourses.
     *
     * @param  Country|null $country
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('countries.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Country can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('countries.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Country can update resourses.
     *
     * @param  User $auth
     * @param  Country $country
     * @return Response
     */
    public function update(User $auth, Country $country)
    {
        return $auth->can('countries.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Country can delete resourses.
     *
     * @param  User $auth
     * @param  Country $country
     * @return Response
     */
    public function delete(User $auth, Country $country)
    {
        return $auth->can('countries.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    /**
     * Determine whether the Country can delete translations of resourses.
     *
     * @param  User $auth
     * @param  Country $country
     * @return Response
     */
    public function deleteTranslation(User $auth, Country $country)
    {
        return $auth->can('countries.delete_translation')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function disable(User $auth, Country $country)
    {
        return $auth->can('countries.disable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function enable(User $auth, Country $country)
    {
        return $auth->can('countries.enable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Country can restore resourses.
     *
     * @param  User $auth
     * @param  Country $country
     * @return Response
     */
    public function restore(User $auth, Country $country)
    {
        return $auth->can('countries.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Country can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Country $country
     * @return Response
     */
    public function forceDelete(User $auth, Country $country)
    {
        return $auth->can('countries.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Country can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Country $country
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('countries.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
