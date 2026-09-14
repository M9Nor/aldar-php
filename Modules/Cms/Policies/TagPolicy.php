<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Cms\Entities\Tag;
use App\User;

class TagPolicy
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
     * Determine whether the tag can view resourses.
     *
     * @param  Tag|null $tag
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('tags.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the tag can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('tags.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the tag can update resourses.
     *
     * @param  User $auth
     * @param  Tag $tag
     * @return Response
     */
    public function update(User $auth, Tag $tag)
    {
        return $auth->can('tags.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the tag can delete resourses.
     *
     * @param  User $auth
     * @param  Tag $tag
     * @return Response
     */
    public function deleteMultiple(User $auth)
    {

        return $auth->can('tags.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    public function restoreMultiple(User $auth)
    {
        return $auth->can('tags.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    public function delete(User $auth, Tag $tag)
    {

        return $auth->can('tags.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    /**
     * Determine whether the tag can delete translations of resourses.
     *
     * @param  User $auth
     * @param  Tag $tag
     * @return Response
     */
    public function deleteTranslation(User $auth, Tag $tag)
    {
        return $auth->can('tags.delete_translation')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function disable(User $auth, Tag $tag)
    {
        return $auth->can('tags.disable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function enable(User $auth, Tag $tag)
    {
        return $auth->can('tags.enable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the tag can restore resourses.
     *
     * @param  User $auth
     * @param  Tag $tag
     * @return Response
     */
    public function restore(User $auth, Tag $tag)
    {
        return $auth->can('tags.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the tag can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Tag $tag
     * @return Response
     */
    public function forceDelete(User $auth, Tag $tag)
    {
        return $auth->can('tags.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the tag can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Tag $tag
     * @return Response
     */
    public function viewDeleted(User $auth)
    {
        return $auth->can('tags.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    public function cache(User $auth)
    {
        return $auth->can('tags.cache')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function requests(User $auth)
    {
        return $auth->can('tags.requests')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
