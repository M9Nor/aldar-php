<?php

namespace Modules\Permissions\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Validator;
use DB;

use Modules\Permissions\Http\Controllers\PermissionsController;
use Modules\Permissions\Entities\Role as CrudModel;
use Yajra\DataTables\Facades\DataTables;
use Modules\Permissions\Entities\Ability;
use Modules\Permissions\Entities\AbilityGroup;
use Modules\Cms\Classes\ResponseHandler;
use App\User;
use Bouncer;

class RoleController extends PermissionsController
{
    public $attributeNames;

    public function __construct()
    {
        $this->attributeNames = [
            'name'              => __('permissions::roles.fields.name.label'),
            'title'             => __('permissions::roles.fields.title.label'),
            'title.*'           => __('permissions::roles.fields.title.label'),
            'description'       => __('permissions::roles.fields.description.label'),
            'description.*'     => __('permissions::roles.fields.description.label'),
            'color'             => __('permissions::roles.fields.color.label'),
            'permissions'       => __('permissions::roles.fields.permissions.label'),
            'manageable_roles'  => __('permissions::roles.fields.manageable_roles.label'),
        ];

        $this->middleware('auth')->except([]);
        parent::__construct();
    }

    public function index(Request $request)
    {
        // $this->data['types'] = Permission::with('translations')->orderByTranslation('title')->get();

        return view('permissions::admin.roles.index', $this->data);
    }

    public function data(Request $request)
    {
        // dd($request->all());
        $list = CrudModel::with('translations', 'abilities.translations', 'abilities.group.translations');

        $withTrashed = request('trashed', 'hide');

        $list->when(auth()->user()->isNotAn('ROOT'), function($query) {
            $query->not('ROOT');
        });

        $list->when($withTrashed == 'show', function($query) {
            $query->onlyTrashed();
        });

        $datatables = DataTables::of($list);

        $datatables
        ->addIndexColumn() // Adds an incremental first row.
        ->filter(function($q) use ($request) {
            if(!empty($filter = $request->filter) && is_array($filter))
            {
                $filter = collect($filter)->mapWithKeys(function ($item) {
                    return [$item['name'] => $item['value']];
                });

                $q
                ->when(!empty($filter['name']), function($query) use ($filter) {
                    $query->where('name', 'like', "%{$filter['name']}%");
                })
                ->when(!empty($filter['title']), function($query) use ($filter) {
                    $query->whereTranslationLike('title', "%{$filter['title']}%");
                })
                ->when(!empty($filter['description']), function($query) use ($filter) {
                    $query->whereTranslationLike('description', "%{$filter['description']}%");
                });
            }
        })
        ->addColumn('title', function($model) {
            return $model->translateOrFirst()->title;
        })
        ->addColumn('description', function($model) {
            return $model->translateOrFirst()->description ? Str::limit($model->translateOrFirst()->description, 200) : '—';
        })
        ->addColumn('permissions', function($model) {
            $permissions = [];
            foreach($model->abilities->groupBy('group_id') as $key => $chunk)
            {
                // dd($chunk);
                $group = !is_null($first = $chunk->first()) ? $first->group : null;
                if(!is_null($group))
                {
                    $item = [];
                    $item = $group->toArray();
                    $item['permissions'] = $chunk->toArray();

                    $permissions[] = $item;
                }
            }
            return $permissions;
        })
        ->addColumn('actions', function($model) {
            $items = [];
            $actions['dropdown'] = [];
            $actions['icons'] = [];
            if(!$model->trashed())
            {
                if(auth()->user()->can('update', $model))
                {
                    $items[] = array_merge($this->actions['edit'], [
                        'url'   => route('RoleController@edit', ['model' => $model->id]),
                        'id'    => 'edit_' . $model->id
                    ]);
                }

                if(auth()->user()->can('delete', $model))
                {
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('RoleController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
            }
            if($model->trashed())
            {
                if(auth()->user()->can('restore', $model))
                {
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('RoleController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', $model))
                {
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('RoleController@destroy', ['model' => $model->id]),
                        'id'    => 'force_delete_' . $model->id
                    ]);
                }
            }

            if(count($items) > 1)
            {
                $actions['dropdown'] = $items;
            }
            else
            {
                $actions['icons'] = $items;
            }

            return $actions;
        });

        $rawColumns = [];

        $rawColumns[] = 'permissions';
        $rawColumns[] = 'actions';

        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }

    /**
     * The show route exists but no detail page does: 404 instead of 500 (S11).
     */
    public function show()
    {
        abort(404);
    }

    public function create(Request $request)
    {
        // Checks if the authenticated user is allowed to proceed farther.
        $this->authorize('create', CrudModel::class);

        // Gets only the roles that current user has.
        $this->data['roles'] = CrudModel::with('translations')->when(auth()->user()->isNotAn('ROOT'), function($query) {
            $query->whereIn('name', auth()->user()->roles->pluck('manageableRoles')->flatten()->pluck('name'));
        })->get();

        $this->data['loggedUserAbilities'] = auth()->user()->getAbilities()->pluck('name');

        $this->data['permissions'] = Ability::with('translations', 'group.translations')->get();

        $this->data['permsGroups'] = AbilityGroup::with([
            'translations',
            'abilities' => function($q) {
                $q->with('translations')->when(auth()->user()->isNotAn('ROOT') && ! config('permissions.config.can_update_all_permissions'), function($query) {
                    $query->whereIn('name', $this->data['loggedUserAbilities']);
                });
            }
        ])
        ->when(auth()->user()->isNotAn('ROOT') && ! config('permissions.config.can_update_all_permissions'), function($query) {
            $query->whereHas('abilities', function($ability) {
                $ability->whereIn('name', $this->data['loggedUserAbilities']);
            });
        })
        ->get();

        return view('permissions::admin.roles.create', $this->data);
    }

    public function store(Request $request)
    {
        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('create', CrudModel::class);

        $rules = [
            'name'              => 'required|string|max:191|unique:perms_abilities,name',
            'title'             => 'required|array',
            'title.*'           => 'required|string|max:100',
            'description'       => 'required|array',
            'description.*'     => 'nullable|string|max:500',
            'color'             => 'nullable|string|max:50',
            'permissions'       => 'nullable|array',
            'manageable_roles'  => 'nullable|array',
        ];

        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);

        if($validator->fails())
        {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'validation_error',
                'title'         => __('cms::messages.validation_error.title'),
                'description'   => __('cms::messages.validation_error.description'),
                'errors'        => $validator->getMessageBag()->toArray()
            ], 422);
        }

        try {
            DB::transaction(function() use ($request) {

                $this->data['model']           = new CrudModel;
                $this->data['model']->name     = $request->name;
                $this->data['model']->color     = $request->color;

                foreach($this->supportedLocales->keys() as $locale)
                {
                    $this->data['model']->{"title:{$locale}"} = $request->title[$locale];
                    $this->data['model']->{"description:{$locale}"} = $request->description[$locale];
                }

                $this->data['model']->save();

                Bouncer::sync($this->data['model'])->abilities(request('permissions', []));

                $this->data['model']->manageableRoles()->sync(request('manageable_roles', []));

                $authRoles = auth()->user()->roles()->whereHas('abilities', function($ability) {
                    $ability->where('name', 'roles.create');
                })->get();

                foreach($authRoles as $role)
                {
                    $role->manageableRoles()->attach($this->data['model']->id);
                }
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.update_error.title'),
                'description'   => config('debug.enabled') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.update_error.description')
            ]);
        }

        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.update_success.title'),
            'description'   => __('cms::messages.update_success.description'),
            'redirect_url'  => request('redirect_url', 'javascript:;')
        ]);
    }

    public function edit(Request $request)
    {
        $this->data['model'] = CrudModel::with('translations', 'abilities.translations', 'manageableRoles', 'roles')->findOrFail($request->model);

        // Checks if the authenticated user is allowed to proceed farther.
        $this->authorize('update', $this->data['model']);

        // Gets only the roles that current user has.
        $this->data['roles'] = CrudModel::with('translations')->when(auth()->user()->isNotAn('ROOT'), function($query) {
            $query->whereIn('name', auth()->user()->roles->pluck('manageableRoles')->flatten()->pluck('name'));
        })->get();

        $this->data['loggedUserAbilities'] = auth()->user()->getAbilities()->pluck('name');

        // Gets only the permissions that this role has.
        $this->data['permsGroups'] = AbilityGroup::with([
            'translations',
            'abilities' => function($q) {
                $q->with('translations')->when(auth()->user()->isNotAn('ROOT') && ! config('permissions.config.can_update_all_permissions'), function($query) {
                    $query->whereIn('name', $this->data['loggedUserAbilities']);
                });
            }
        ])
        ->when(auth()->user()->isNotAn('ROOT') && ! config('permissions.config.can_update_all_permissions'), function($query) {
            $query->whereHas('abilities', function($ability) {
                $ability->whereIn('name', $this->data['loggedUserAbilities']);
            });
        })
        ->get();
        // dd(auth()->user()->getAbilities()->pluck('name'), $this->data['permsGroups']);
        return view('permissions::admin.roles.edit', $this->data);
    }

    public function update(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail($request->model);

        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('update', $this->data['model']);

        $rules = [
            'name'              => 'required|string|max:191|unique:perms_abilities,name,'.$request->model,
            'title'             => 'required|array',
            'title.*'           => 'required|string|max:100',
            'description'       => 'required|array',
            'description.*'     => 'nullable|string|max:500',
            'color'             => 'nullable|string|max:50',
            'permissions'       => 'nullable|array',
            'manageable_roles'  => 'nullable|array',
        ];

        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);

        if($validator->fails())
        {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'validation_error',
                'title'         => __('cms::messages.validation_error.title'),
                'description'   => __('cms::messages.validation_error.description'),
                'errors'        => $validator->getMessageBag()->toArray()
            ], 422);
        }

        try {

            DB::transaction(function() use ($request) {

                $this->data['model']->name = $request->name;
                $this->data['model']->color = $request->color;

                foreach($this->supportedLocales->keys() as $locale)
                {
                    $this->data['model']->{"title:{$locale}"} = $request->title[$locale];
                    $this->data['model']->{"description:{$locale}"} = $request->description[$locale];
                }

                $this->data['model']->save();

                Bouncer::sync($this->data['model'])->abilities(request('permissions', []));

                $this->data['model']->manageableRoles()->sync(request('manageable_roles', []));
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.save_error.title'),
                'description'   => config('debug.enabled') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.save_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.save_success.title'),
            'description'   => __('cms::messages.save_success.description'),
            'redirect_url'  => request('redirect_url', 'javascript:;')
        ]);
    }

    public function destroy(Request $request)
    {
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);

        // Check if the authenticated user is allowed to proceed farther.
        if(!$this->data['model']->trashed())
        {
            $this->authorize('delete', $this->data['model']);

            try {
                DB::transaction(function() use ($request) {
                    $this->data['model']->delete();
                });
            } catch (\Exception $e) {
                return new ResponseHandler([
                    'success'     => false,
                    'type'        => 'danger',
                    'title'       => __('cms::messages.delete_error.title'),
                    'description' => __('cms::messages.delete_error.description')
                ]);
            }
        }
        else
        {
            // Check if the authenticated user is allowed to proceed farther.
            $this->authorize('forceDelete', $this->data['model']);

            try {
                DB::transaction(function() use ($request) {
                    $this->data['model']->forceDelete();
                });
            } catch (\Exception $e) {
                return new ResponseHandler([
                    'success'     => false,
                    'type'        => 'danger',
                    'title'       => __('cms::messages.delete_error.title'),
                    'description' => __('cms::messages.delete_error.description')
                ]);
            }
        }

        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.delete_success.title'),
            'description' => __('cms::messages.delete_success.description')
        ]);
    }

    public function restore(Request $request)
    {
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);
        $this->authorize('restore', $this->data['model']);
        if(!$this->data['model']->trashed())
        {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.restore_error.title'),
                'description' => __('cms::messages.restore_error.description')
            ]);
        }

        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->restore();
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.restore_error.title'),
                'description' => __('cms::messages.restore_error.description')
            ]);
        }

        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.restore_success.title'),
            'description' => __('cms::messages.restore_success.description')
        ]);
    }

    public function massDestroy(Request $request)
    {
        $this->data['models'] = CrudModel::withTrashed()->whereIn('id', explode(',', $request->ids))->get();

        // The model attribute which will be shown to user to indicate the unsuccessful models.
        $this->data['attribute'] = 'title';
        $this->data['failed'] = collect([]);

        // Loop through the selected models to determine which of whom can be deleted.
        foreach($this->data['models'] as $model)
        {
            if(!$model->trashed())
            {
                try {
                    // Check if the authenticated user is allowed to proceed farther.
                    $this->authorize('delete', $model);

                    DB::transaction(function() use ($request, $model) {
                        $model->delete();
                    });

                } catch (\Exception $e) {
                    // Push failed models to failed array to notify the user which models could not be deleted.
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }

            }
            else
            {
                try {
                    // Check if the authenticated user is allowed to proceed farther.
                    $this->authorize('forceDelete', $model);

                    DB::transaction(function() use ($request, $model) {
                        $model->forceDelete();
                        if($model->image) app()->make('GraphManager')->delete($model->image);
                    });

                } catch (\Exception $e) {
                    // Push failed models to failed array to notify the user which models could not be deleted.
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }

            }
        }

        if($this->data['failed']->count() > 0)
        {
            $failed = '';

            foreach($this->data['failed'] as $item)
            {
                $failed .= '<span class="kt-badge kt-badge--dark kt-badge--inline">' . $item . '</span> ';
            }

            return new ResponseHandler([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.mass_delete_error.title'),
                'description' => __('cms::messages.mass_delete_error.description') . '<div class="mt-2">' . $failed . '</div>'
            ]);
        }
        else
        {
            return new ResponseHandler([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.mass_delete_success.title'),
                'description' => __('cms::messages.mass_delete_success.description')
            ]);
        }
    }

    public function massRestore(Request $request)
    {
        $this->data['models'] = CrudModel::onlyTrashed()->whereIn('id', explode(',', $request->ids))->get();

        // The model attribute which will be shown to user to indicate the unsuccessful models.
        $this->data['attribute'] = 'title';
        $this->data['failed'] = collect([]);

        // Loop through the selected models to determine which of whom can be restored.
        foreach($this->data['models'] as $model)
        {
            try {
                // Check if the authenticated user is allowed to proceed farther.
                $this->authorize('restore', $model);

                DB::transaction(function() use ($request, $model) {
                    $model->restore();
                });

            } catch (\Exception $e) {
                // Push failed models to failed array to notify the user which models could not be restored.
                $this->data['failed']->push($model->{$this->data['attribute']});
            }
        }

        if($this->data['failed']->count() > 0)
        {
            $failed = '';

            foreach($this->data['failed'] as $item)
            {
                $failed .= '<span class="kt-badge kt-badge--dark kt-badge--inline">' . $item . '</span> ';
            }

            return new ResponseHandler([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.mass_restore_error.title'),
                'description' => __('cms::messages.mass_restore_error.description') . '<div class="mt-2">' . $failed . '</div>'
            ]);
        }
        else
        {
            return new ResponseHandler([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.mass_restore_success.title'),
                'description' => __('cms::messages.mass_restore_success.description')
            ]);
        }
    }
}
