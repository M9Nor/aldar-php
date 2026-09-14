<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Cms\Entities\LandingPage;
use App\User;

class LandingPagePolicy
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
     * Determine whether the LandingPage can view resourses.
     *
     * @param  LandingPage|null $landing_page
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('landingpages.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the LandingPage can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('landingpages.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the LandingPage can update resourses.
     *
     * @param  User $auth
     * @param  LandingPage $landing_page
     * @return Response
     */
    public function update(User $auth, LandingPage $landing_page)
    {
        return $auth->can('landingpages.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the LandingPage can delete resourses.
     *
     * @param  User $auth
     * @param  LandingPage $landing_page
     * @return Response
     */
    public function delete(User $auth, LandingPage $landing_page)
    {
        return $auth->can('landingpages.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    /**
     * Determine whether the landingPage can delete translations of resourses.
     *
     * @param  User $auth
     * @param  LandingPage $landing_page
     * @return Response
     */
    public function deleteTranslation(User $auth, LandingPage $landing_page)
    {
        return $auth->can('landingpages.delete_translation')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function disable(User $auth, LandingPage $landing_page)
    {
        return $auth->can('landingpages.disable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function enable(User $auth, LandingPage $landing_page)
    {
        return $auth->can('landingpages.enable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the LandingPage can restore resourses.
     *
     * @param  User $auth
     * @param  LandingPage $landing_page
     * @return Response
     */
    public function restore(User $auth, LandingPage $landing_page)
    {
        return $auth->can('landingpages.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the landingPage can delete resourses permanently.
     *
     * @param  User $auth
     * @param  LandingPage $landing_page
     * @return Response
     */
    public function forceDelete(User $auth, LandingPage $landing_page)
    {
        return $auth->can('landingpages.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the landingPage can delete resourses permanently.
     *
     * @param  User $auth
     * @param  LandingPage $landing_page
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('landingpages.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
