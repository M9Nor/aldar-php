<?php

namespace Modules\Notification\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Notification\Entities\FirebaseNotification;
use App\User;
use Bouncer;

class NotificationPolicy
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
     * @param  FirebaseNotification|null $notification
     * @return Response
     */
    public function view(User $auth)
    {
        return $auth->can('notifications.view')
            ? Response::allow()
            : Response::deny('notification::messages.permission_error', 403);
    }

    /**
     * Determine whether the user can create resourses.
     *
     * @param  User $auth
     * @return Response
     */
    public function create(User $auth)
    {
        return $auth->can('notifications.create')
            ? Response::allow()
            : Response::deny('notification::messages.permission_error', 403);
    }
}
