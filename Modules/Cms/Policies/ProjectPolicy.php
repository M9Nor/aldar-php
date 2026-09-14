<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Backend\Entities\Project;
use App\User;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        return 1;
    }

    /**
     * Determine whether the Project can view resourses.
     *
     * @param  Project|null $project
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('projects.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Project can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('projects.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Project can update resourses.
     *
     * @param  User $auth
     * @param  Project $project
     * @return Response
     */
    public function update(User $auth, Project $project)
    {
        return $auth->can('projects.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Project can delete resourses.
     *
     * @param  User $auth
     * @param  Project $project
     * @return Response
     */
    public function delete(User $auth, Project $project)
    {
        return $auth->can('projects.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    /**
     * Determine whether the Project can delete translations of resourses.
     *
     * @param  User $auth
     * @param  Project $project
     * @return Response
     */
    public function deleteTranslation(User $auth, Project $project)
    {
        return $auth->can('projects.delete_translation')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function disable(User $auth, Project $project)
    {
        return $auth->can('projects.disable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function enable(User $auth, Project $project)
    {
        return $auth->can('projects.enable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Project can restore resourses.
     *
     * @param  User $auth
     * @param  Project $project
     * @return Response
     */
    public function restore(User $auth, Project $project)
    {
        return $auth->can('projects.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Project can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Project $project
     * @return Response
     */
    public function forceDelete(User $auth, Project $project)
    {
        return $auth->can('projects.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the Project can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Project $project
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('projects.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
