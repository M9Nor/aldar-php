<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Cms\Entities\Content;
use App\User;

class ContentPolicy
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
     * Determine whether the content can view resourses.
     *
     * @param  Content|null $content
     * @return Response
     */
    public function view(User $auth, $type = null)
    {
        if(is_null($type)) return true;
        return $auth->can('contents.'.$type.'.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the content can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth, $type = null)
    {
        if(is_null($type)) return true;
        return $auth->can('contents.'.$type.'.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the content can update resourses.
     *
     * @param  User $auth
     * @param  Content $content
     * @return Response
     */
    public function update(User $auth, Content $content)
    {
        return $auth->can('contents.'.$content->type.'.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the content can delete resourses.
     *
     * @param  User $auth
     * @param  Content $content
     * @return Response
     */
    public function delete(User $auth, Content $content)
    {
        return $auth->can('contents.'.$content->type.'.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    /**
     * Determine whether the content can delete translations of resourses.
     *
     * @param  User $auth
     * @param  Content $content
     * @return Response
     */
    public function deleteTranslation(User $auth, Content $content)
    {
        return $auth->can('contents.'.$content->type.'.delete_translation')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function disable(User $auth, Content $content)
    {
        return $auth->can('contents.'.$content->type.'.disable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
    public function enable(User $auth, Content $content)
    {
        return $auth->can('contents.'.$content->type.'.enable')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the content can restore resourses.
     *
     * @param  User $auth
     * @param  Content $content
     * @return Response
     */
    public function restore(User $auth, Content $content)
    {
        return $auth->can('contents.'.$content->type.'.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the content can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Content $content
     * @return Response
     */
    public function forceDelete(User $auth, Content $content)
    {
        return $auth->can('contents.'.$content->type.'.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the content can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Content $content
     * @return Response
     */
    public function viewDeleted(User $auth, $type = null)
    {
        if(is_null($type)) return true;
        return $auth->can('contents.'.$type.'.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
