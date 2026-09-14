<?php

namespace Modules\Cms\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Cms\Entities\Category;
use App\User;

class CategoryPolicy
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
     * Determine whether the category can view resourses.
     *
     * @param  Category|null $category
     * @return Response
     */
    public function view(User $auth, $type = null)
    {
        return $auth->can('categories.'.$type.'.view')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the category can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth, $type = null)
    {
        if(is_null($type)) return true;
        return $auth->can('categories.'.$type.'.create')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the category can update resourses.
     *
     * @param  User $auth
     * @param  Category $category
     * @return Response
     */
    public function update(User $auth, Category $category)
    {
        return $auth->can('categories.'.$category->type.'.edit')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the category can delete resourses.
     *
     * @param  User $auth
     * @param  Category $category
     * @return Response
     */
    public function delete(User $auth, Category $category)
    {
        return $auth->can('categories.'.$category->type.'.delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the category can restore resourses.
     *
     * @param  User $auth
     * @param  Category $category
     * @return Response
     */
    public function restore(User $auth, Category $category)
    {
        return $auth->can('categories.'.$category->type.'.restore')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the category can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Category $category
     * @return Response
     */
    public function forceDelete(User $auth, Category $category)
    {
        return $auth->can('categories.'.$category->type.'.force_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }

    /**
     * Determine whether the category can delete resourses permanently.
     *
     * @param  User $auth
     * @param  Category $category
     * @return Response
     */
    public function viewDeleted(User $auth, $type = null)
    {
        if(is_null($type)) return true;
        return $auth->can('categories.'.$type.'.view_delete')
            ? Response::allow()
            : Response::deny('permissions::messages.permission_error', 403);
    }
}
