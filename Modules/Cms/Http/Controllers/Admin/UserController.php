<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

use Modules\Cms\Http\Controllers\CmsController;
use Modules\Permissions\Entities\Role;
use Modules\Cms\Entities\UserType;
use Modules\Cms\Classes\ResponseHandler;
use Modules\Cms\Rules\Username;
use App\User as CrudModel;
use Validator;
use Bouncer;
use Auth;
use DB;

class UserController extends CmsController
{
    public $attributeNames;

    public function __construct()
    {
        $this->attributeNames = [
            'username'      => __('cms::users.fields.username.label'),
            'first_name'    => __('cms::users.fields.first_name.label'),
            'last_name'     => __('cms::users.fields.last_name.label'),
            'email'         => __('cms::users.fields.email.label'),
            'phone'         => __('cms::users.fields.phone.label'),
            'password'      => __('cms::users.fields.password.label'),
            'roles'         => __('cms::users.fields.roles.label'),
            'roles.*'       => __('cms::users.fields.roles.label'),
            'image'         => __('cms::users.fields.image.label'),
            'address'       => __('cms::users.fields.address.label'),
        ];

        $this->middleware('auth')->except([]);
        parent::__construct();
    }

    public function summary(Request $request)
    {
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model)->toArray();

        $this->data['summary'] = view('cms::admin.users.summary', $this->data)->render();

        return new ResponseHandler([
            'success'   => true,
            'model'     => $this->data['model'],
            'summary'   => $this->data['summary']
        ]);
    }

    public function validateIdentity_(Request $request)
    {
        $validator = Validator::make([
            $request->name => $request->keyword
        ], [
            'username'  => ['nullable', 'min:4', 'max:191', new Username, 'unique:users,username' .$this->id],
            'email'     => ['nullable', 'max:191', 'email', 'unique:users,email'  .$this->id]
        ]);

        return new ResponseHandler([
            'success'   => true,
            'is_valid'  => $validator->passes(),
            'error'     => $validator->fails() ? $validator->getMessageBag()->first() : null
        ]);
    }
    public function validateIdentity(Request $request)
    {
        $validator = Validator::make([
            $request->name => $request->keyword
        ], [
            'username'  => ['nullable', 'min:4', 'max:191', new Username, 'unique:users,username'],
            'email'     => ['nullable', 'max:191', 'email', 'unique:users,email']
        ]);

        return new ResponseHandler([
            'success'   => true,
            'is_valid'  => $validator->passes(),
            'error'     => $validator->fails() ? $validator->getMessageBag()->first() : null
        ]);
    }

    public function index(Request $request)
    {
        $this->data['statuses'] = CrudModel::collectStatuses();

        return view('cms::admin.users.index', $this->data);
    }

    public function data(Request $request)
    {
        // dd($request->all());
        $list = CrudModel::where('id','!=',8);

        $withTrashed = request('trashed', 'hide');

        // Exclude ROOT users from query when the logged user is not ROOT.
        $list->when(auth()->user()->isNotAn('ROOT'), function($query) {
            $query->whereDoesntHave('roles', function($role) {
                $role->where('name', 'ROOT');
            });
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
                    $query->where('first_name', 'like', "%{$filter['name']}%");
                    $query->orWhere('last_name', 'like', "%{$filter['name']}%");
                    $query->orWhere('username', 'like', "%{$filter['name']}%");
                })
                ->when(!empty($filter['email']), function($query) use ($filter) {
                    $query->where('email', 'like', "%{$filter['email']}%");
                })
                ->when(!empty($filter['status']), function($query) use ($filter) {
                    $query->where('status', 'like', "%{$filter['status']}%");
                });
            }
        })
        ->addColumn('user_image', function($model){
            return [
                'src' => $model->getImage('75x75')
            ];
        })
        ->addColumn('user', function(CrudModel $model) {
            return json_encode($model);
        })
        ->addColumn('user_status', function($model){
            return [
                'label' => __('cms::users.statuses.' . $model->status . '.label'),
                'color' => __('cms::users.statuses.' . $model->status . '.color'),
            ];
        })
        ->addColumn('actions', function($model){
            $items = [];
            $actions['dropdown'] = [];
            $actions['icons'] = [];
            if(!$model->trashed())
            {
                if(auth()->user()->can('update', $model))
                {
                    $items[] = array_merge($this->actions['edit'], [
                        'url'   => route('UserController@edit', ['model' => $model->id]),
                        'id'    => 'edit_' . $model->id
                    ]);
                }

                if(auth()->user()->can('delete', $model) && auth()->user()->id != $model->id)
                {
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('UserController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
            }
            if($model->trashed())
            {
                if(auth()->user()->can('restore', $model) && auth()->user()->id != $model->id)
                {
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('UserController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', $model) && auth()->user()->id != $model->id)
                {
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('UserController@destroy', ['model' => $model->id]),
                        'id'    => 'force_delete_' . $model->id
                    ]);
                }
            }

            // Gives the ROOT user the ability to login with any other user without a password.
            if(auth()->user()->isA('ROOT') && auth()->user()->id != $model->id)
            {
                $items[] = [
                    'label'     => __('cms::global.actions.login_as', ['name' => $model->full_name]),
                    'color'     => 'success',
                    'icon'      => 'fa fa-lg fa-fw fa-door-open',
                    'url'       => route('UserController@loginAs', ['model' => $model->id]),
                    'id'        => 'login_as_' . $model->id,
                    'action'    => 'loginAs',
                    'divider'   => true
                ];
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

        $rawColumns[] = 'user';
        $rawColumns[] = 'actions';

        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function getUsersSelect2(Request $request){
        $term = trim((string) $request->search);
        $page = $request->page;
        if(!$page){
            $page = 1;
        }
        $list = CrudModel::where(function($q) use ($term) {
            $q->where('username','LIKE', "%{$term}%");
        })->orWhere('id',$term);

        $list = $list->paginate($request->items_per_page);
        $result['results'] = [];
        foreach($list as $key => $item){
            $result['results'][$key] = $item->formAjaxArray(true);
        }
        $last_page = $list->lastPage();
        $result['pagination']['more'] = $page >= $last_page ? false : true;
        return json_encode($result);
    }
    public function create(Request $request)
    {
        // dd(
        //     \Modules\Cms\Entities\Content::types(),
        //     \Modules\Cms\Entities\Content::typeExists(),
        //     \Modules\Cms\Entities\Content::getTypeFields('STORIES'),
        //     \Modules\Cms\Entities\Content::getTypeFieldNames('STORIES'),
        //     \Modules\Cms\Entities\Content::typeHasField('STORIES', 'type'),
        //     \Modules\Cms\Entities\Content::getFieldLabel('STORIES', 'title'),
        //     \Modules\Cms\Entities\Content::getFieldPlaceholder('STORIES', 'title'),
        //     \Modules\Cms\Entities\Content::getFieldHelp('STORIES', 'title')
        // );
        $this->authorize('create', CrudModel::class);

        $this->data['role'] = strtoupper((string) $request->role);

        if(!$request->role)
        {
            $this->data['roles'] = Role::with('translations');

            if(auth()->user()->isNotA('ROOT'))
            {
                $this->data['roles']->where('name', '!=', 'ROOT');
            }

            $this->data['roles'] = $this->data['roles']->orderBy('id', 'desc')->get();
        }

        $this->data['sexOptions'] = CrudModel::collectSexOptions();
        $this->data['statuses'] = CrudModel::collectStatuses();

        return view('cms::admin.users.create', $this->data);
    }

    public function store(Request $request)
    {
        // dd($request->all());

        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('create', CrudModel::class);

        \Log::debug($request->all());
        $rules = [
            'username'      => 'required|string|max:191|unique:users,username',
            'first_name'    => 'nullable|string|max:20',
            'last_name'     => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'required|string|email|max:191|unique:users,email',
            'password'      => 'required|string|min:8|max:20|confirmed',
            'address'       => 'nullable|string|max:256',
            'roles'         => 'required|array|min:1',
            'roles.*'       => 'nullable|exists:perms_roles,name',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'users_test'    => 'nullable|array',
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
                $this->data['model']                    = new CrudModel;
                $this->data['model']->username          = $request->username;
                $this->data['model']->first_name        = $request->first_name;
                $this->data['model']->last_name         = $request->last_name;
                $this->data['model']->phone             = $request->phone;
                $this->data['model']->email             = $request->email;
                $this->data['model']->password          = bcrypt($request->password);
                $this->data['model']->address           = $request->address;
                $this->data['model']->verification_code = 'VERIFIED';
                $this->data['model']->status            = $request->status;

                if($request->hasFile('image'))
                {
                    $this->data['imagePath'] = $request->file('image')->store('users');
                    $this->data['model']->image = $this->data['imagePath'];
                }

                $this->data['model']->save();

                Bouncer::sync($this->data['model'])->roles($request->roles);
            });
        } catch (\Exception $e) {
            if(array_key_exists('imagePath', $this->data)) app()->ImageManipulator->deleteImage($this->data['imagePath']);

            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.update_error.title'),
                'description'   => env('APP_DEBUG') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.update_error.description')
            ]);
        }

        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.update_success.title'),
            'description'   => __('cms::messages.update_success.description'),
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }
    public function myprofile(Request $request)
    {

        $this->data['role'] = strtoupper((string) $request->role);

        if(!$request->role)
        {
            $this->data['roles'] = Role::with('translations');

            if(auth()->user()->isNotA('ROOT'))
            {
                $this->data['roles']->where('name', '!=', 'ROOT');
            }

            $this->data['roles'] = $this->data['roles']->orderBy('id', 'desc')->get();
        }


        $this->data['sexOptions'] = CrudModel::collectSexOptions();
        $this->data['statuses'] = CrudModel::collectStatuses();
        $this->data['model'] = CrudModel::withTrashed()->findOrFail(Auth::user()->id);

        return view('cms::admin.users.update_profile', $this->data);
    }
    public function edit(Request $request)
    {

        $this->data['role'] = strtoupper((string) $request->role);

        if(!$request->role)
        {
            $this->data['roles'] = Role::with('translations');

            if(auth()->user()->isNotA('ROOT'))
            {
                $this->data['roles']->where('name', '!=', 'ROOT');
            }

            $this->data['roles'] = $this->data['roles']->orderBy('id', 'desc')->get();
        }


        $this->data['sexOptions'] = CrudModel::collectSexOptions();
        $this->data['statuses'] = CrudModel::collectStatuses();
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);

        return view('cms::admin.users.update', $this->data);
    }

    public function update(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail("{$request->model}");

        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('update', $this->data['model']);

        \Log::debug($request->all());

        $rules = [
            'username'      => 'nullable|string|max:191',
            'first_name'    => 'nullable|string|max:20',
            'last_name'     => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|string|email|max:191',
            'password'      => 'nullable|string|min:8|max:20|confirmed',
            'address'       => 'nullable|string|max:256',
            'roles'         => 'nullable|array|min:1',
            'roles.*'       => 'nullable|exists:perms_roles,name',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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
                $this->data['model']->username          = $request->username;
                $this->data['model']->first_name        = $request->first_name;
                $this->data['model']->last_name         = $request->last_name;
                $this->data['model']->phone             = $request->phone;
                $this->data['model']->email             = $request->email;
                if(!empty($request->password)){
                    $this->data['model']->password          = bcrypt($request->password);
                }
                $this->data['model']->address           = $request->address;
                $this->data['model']->verification_code = 'VERIFIED';
                $this->data['model']->status            = $request->status;

                if($request->hasFile('image'))
                {
                    $this->data['imagePath'] = $request->file('image')->store('users');
                    $this->data['model']->image = $this->data['imagePath'];
                }

                $this->data['model']->save();

                Bouncer::sync($this->data['model'])->roles($request->roles);
            });
        } catch (\Exception $e) {
            if(array_key_exists('imagePath', $this->data)) app()->ImageManipulator->deleteImage($this->data['imagePath']);

            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.update_error.title'),
                'description'   => env('APP_DEBUG') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.update_error.description')
            ]);
        }

        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.update_success.title'),
            'description'   => __('cms::messages.update_success.description'),
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }
    public function updateProfile(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail(Auth::user()->id);

        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('update', $this->data['model']);

        \Log::debug($request->all());
        $rules = [
            'username'      => 'nullable|string|max:191',
            'first_name'    => 'nullable|string|max:20',
            'last_name'     => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|string|email|max:191',
            'password'      => 'nullable|string|min:8|max:20|confirmed',
            'address'       => 'nullable|string|max:256',

            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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
                $this->data['model']->username          = $request->username;
                $this->data['model']->first_name        = $request->first_name;
                $this->data['model']->last_name         = $request->last_name;
                $this->data['model']->phone             = $request->phone;
                $this->data['model']->email             = $request->email;
                if(!empty($request->password)){
                    $this->data['model']->password          = bcrypt($request->password);
                }
                $this->data['model']->address           = $request->address;

                if($request->hasFile('image'))
                {
                    $this->data['imagePath'] = $request->file('image')->store('users');
                    $this->data['model']->image = $this->data['imagePath'];
                }

                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            if(array_key_exists('imagePath', $this->data)) app()->ImageManipulator->deleteImage($this->data['imagePath']);

            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.update_error.title'),
                'description'   => env('APP_DEBUG') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.update_error.description')
            ]);
        }

        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.update_success.title'),
            'description'   => __('cms::messages.update_success.description'),
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }

    public function destroy(Request $request)
    {
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);

        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('delete', $this->data['model']);

        if(!$this->data['model']->trashed())
        {
            try {
                DB::transaction(function() use ($request) {
                    $this->data['model']->delete();
                });
            } catch (\Exception $e) {
                return response()->json([
                    'success'     => false,
                    'type'        => 'danger',
                    'title'       => __('cms::messages.delete_error.title'),
                    'description' => __('cms::messages.delete_error.description')
                ]);
            }

            return response()->json([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.delete_success.title'),
                'description' => __('cms::messages.delete_success.description')
            ]);
        }

        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('forceDelete', $this->data['model']);

        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->forceDelete();
                if($this->data['model']->image) app()->ImageManipulator->deleteImage($this->data['model']->image);
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return response()->json([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.delete_error.title'),
                'description' => __('cms::messages.delete_error.description')
            ]);
        }

        return response()->json([
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
            return response()->json([
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
            return response()->json([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.restore_error.title'),
                'description' => __('cms::messages.restore_error.description')
            ]);
        }

        return response()->json([
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
        $this->data['attribute'] = 'full_name';
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

            return response()->json([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.mass_delete_error.title'),
                'description' => __('cms::messages.mass_delete_error.description') . '<div class="mt-2">' . $failed . '</div>'
            ]);
        }
        else
        {
            return response()->json([
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
        $this->data['attribute'] = 'full_name';
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

            return response()->json([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.mass_restore_error.title'),
                'description' => __('cms::messages.mass_restore_error.description') . '<div class="mt-2">' . $failed . '</div>'
            ]);
        }
        else
        {
            return response()->json([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.mass_restore_success.title'),
                'description' => __('cms::messages.mass_restore_success.description')
            ]);
        }
    }

    /*
    * Credit: Idea by colleague, Mustafa Omar.
    */
    public function loginAs(Request $request)
    {
        /**
         * If a temporary-login user session exists remove it and return login to ROOT user.
         * Removes temporary user from sesson.
         */
        if(session()->get('temporaryLoginUser'))
        {
            auth()->loginUsingId(session()->remove('temporaryLoginUser'));

            session()->remove('temporaryLoginUser');

            return redirect()->back();
        }

        /**
         * Give ROOT user the ability to login to all other users without password.
         * Clones selected user and create a cloned session.
         */
        if(auth()->user()->isA('ROOT'))
        {
            session()->put('temporaryLoginUser', auth()->user()->id);

            auth()->loginUsingId($request->model, true);

            return redirect()->back();
        }
    }
}
