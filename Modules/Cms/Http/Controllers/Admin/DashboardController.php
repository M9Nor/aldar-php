<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\User;
use Modules\Permissions\Entities\Role;
use Modules\Permissions\Entities\Ability;
use Modules\Cms\Http\Controllers\CmsController;

use Illuminate\Support\Str;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\Content;
use Modules\Permissions\Entities\AbilityGroup;

class DashboardController extends CmsController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        // updateCurrency is gated by the route's own 'staff' middleware, which already requires
        // an authenticated request and returns a JSON-aware 401 to anonymous AJAX callers; the
        // blanket 'auth' middleware here would otherwise intercept it first and always redirect.
        $this->middleware('auth')->except(['updateCurrency']);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->data['total_users'] = User::count();
        $this->data['total_roles'] = Role::count();
        $this->data['total_permissions'] = Ability::count();
        return view('cms::dashboard', $this->data);
    }

    /**
     * Refresh currency rates on demand (the same command the scheduler runs).
     */
    public function updateCurrency()
    {
        \Artisan::call('update_currency');

        return redirect()->route('DashboardController@index');
    }
}
