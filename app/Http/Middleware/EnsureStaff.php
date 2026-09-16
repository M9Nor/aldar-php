<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;

/**
 * Admin-only endpoints: an active, non-deleted account holding a staff role.
 * ROOT is deliberately not a staff role; it belonged to the former vendor.
 */
class EnsureStaff
{
    public const ROLES = ['SUPERADMIN', 'ADMIN', 'Editor'];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user() ?? $this->disabledOrDeletedSessionUser($request);

        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401)
                : redirect()->guest(route('login'));
        }

        if ($user->disabled_at !== null || $user->deleted_at !== null || ! $user->isAn(...self::ROLES)) {
            // A plain response, not abort(403): this app's exception Handler rewrites every
            // HttpException (including abort()'s) into a 302 "session expired" redirect, which
            // would let a disabled/deleted/wrong-role staff member look identical to a denied
            // anonymous one instead of getting a real 403.
            // JSON callers get the same {success, message} shape as the 401 branch above (S8).
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Forbidden.'], 403)
                : response('Forbidden.', 403);
        }

        return $next($request);
    }

    /**
     * The `users` provider's default query excludes disabled and soft-deleted accounts
     * (see Modules\Cms\Entities\Scopes\Disabable and the User model's SoftDeletes trait),
     * so a disabled/deleted staff member's session looks identical to a logged-out one to
     * $request->user(). Re-resolve the session's raw user id, bypassing those scopes, so we
     * can tell the two cases apart and return 403 rather than treating them as unauthenticated.
     */
    protected function disabledOrDeletedSessionUser(Request $request): ?User
    {
        $guard = auth()->guard();

        if (! $guard instanceof SessionGuard) {
            return null;
        }

        $id = $request->session()->get($guard->getName());

        return $id ? User::withDisabled()->withTrashed()->find($id) : null;
    }
}
