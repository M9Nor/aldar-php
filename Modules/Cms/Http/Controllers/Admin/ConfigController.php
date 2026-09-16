<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Cms\Http\Controllers\CmsController;
use Modules\Cms\Entities\UserType;
use Modules\Cms\Classes\ResponseHandler;
use Illuminate\Support\Str;
use Modules\Cms\Entities\Config as CrudModel;
use Validator;
use Bouncer;
use Auth;
use DB;
use LaravelLocalization;
use App\Rules\Slug;

class ConfigController extends CmsController
{
    public $attributeNames = [];
    public function __construct(){
        $this->attributeNames = [
            'input_type'            => __('cms::cruds.config.input_type.label'),
            'config_validate'       => __('cms::cruds.config.validations.label'),
            'sort_order'            => __('cms::cruds.config.sort_order.label'),
            'key'                   => __('cms::cruds.config.key.label'),
            'val'                   => __('cms::cruds.config.val.label'),
            'has_additional_info'   => __('cms::cruds.config.has_additional_info.label'),
        ];
        $translationAttributeNames = [];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $names = [
                'label_'.$locale            => __('cms::cruds.config.label_'.$locale.''),
                'placeholder_'.$locale      => __('cms::cruds.config.placeholder_'.$locale.''),
                'help_'.$locale             => __('cms::cruds.config.help_'.$locale.''),
                'icon_'.$locale             => __('cms::cruds.config.icon_'.$locale.''),
                'title_'.$locale            => __('cms::cruds.config.title_'.$locale.''),
                'description_'.$locale      => __('cms::cruds.config.description_'.$locale.''),
                'image_'.$locale            => __('cms::cruds.config.image_'.$locale.''),
            ];
            $translationAttributeNames = array_merge($translationAttributeNames,$names);
        }
        $this->attributeNames = array_merge($this->attributeNames,$translationAttributeNames);
        $this->middleware('auth')->except([]);
        parent::__construct();
    }
    public function index(Request $request){
        $this->authorize('view', CrudModel::class);
        return view('cms::config.index', $this->data);
    }
    public function data(Request $request){
        $this->authorize('view', CrudModel::class);
        $list = CrudModel::select(['cms_configs.*',
            DB::raw('
                (
                    SELECT trans.title
                    FROM cms_config_translations AS trans
                    WHERE trans.config_id = cms_configs.id
                    AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
            ')
        ])->with('translations');
        $withTrashed = request('trashed', 'hide');
        $list->when($withTrashed == 'show', function($query) {
            $query->onlyTrashed();
        });
        $datatables = DataTables::of($list);
        $datatables
        ->addIndexColumn() // Adds an incremental first row.
        ->filter(function($q) use ($request) {
            if(!empty($filter = $request->filter) && is_array($filter)){
                $filter = collect($filter)->mapWithKeys(function ($item) {
                    return [$item['name'] => $item['value']];
                });
                $q->when(!empty($filter['title']), function($query) use ($filter) {
                    $query->whereTranslationLike('title', "%{$filter['title']}%");
                })->when(!empty($filter['description']), function($query) use ($filter) {
                    $query->whereTranslationLike('description', "%{$filter['description']}%");
                })->when(!empty($filter['key']), function($query) use ($filter) {
                    $query->where('key','LIKE', "%{$filter['key']}%");
                })->when(!empty($filter['val']), function($query) use ($filter) {
                    $query->where('val','LIKE', "%{$filter['val']}%");
                });
            }
        })
        ->addColumn('translated_name', function($model) {
            return $model->translateOrFirst()->label;
        })->addColumn('languages', function($model){
            $output = '';
            foreach($model->translations->sortBy('locale') as $translation){
                if($translation->locale == 'ar'){
                    $output .= ' <span style="font-weight:bold" class="kt-badge kt-badge--inline kt-badge--success"> ' . $translation->locale . ' </span>';
                }elseif($translation->locale == 'en'){
                    $output .= ' <span style="font-weight:bold" class="kt-badge kt-badge--inline kt-badge--info"> ' . $translation->locale . ' </span>';
                }else{
                    $output .= ' <span style="font-weight:bold" class="kt-badge kt-badge--inline kt-badge--primary"> ' . $translation->locale . ' </span>';
                }
            }
            return $output;
        })
        ->addColumn('actions', function($model) {
            $items = [];
            $actions['dropdown']    = [];
            $actions['icons']       = [];
            if(!$model->trashed()){
                if(auth()->user()->can('update', $model)){
                    // $items[] = array_merge($this->actions['edit'], [
                    //     'url'   => route('ConfigController@edit', ['model' => $model->id]),
                    //     'id'    => 'edit_' . $model->id
                    // ]);
                }
                if(auth()->user()->can('update', $model)){
                    $items[] = array_merge($this->actions['edit'], [
                        'url'   => route('ConfigController@editConfig', ['model' => $model->id]),
                        'id'    => 'edit_' . $model->id,
                        'icon'  => 'fa fa-lg fa-fw fa-pencil-alt',
                        'label' => __('cms::cruds.config.edit_config'),
                        'color' => 'success',
                    ]);
                }
                if(auth()->user()->can('delete', $model)){
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('ConfigController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
            }
            if($model->trashed()){
                if(auth()->user()->can('restore', $model)){
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('ConfigController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', $model)){
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('ConfigController@destroy', ['model' => $model->id]),
                        'id'    => 'force_delete_' . $model->id
                    ]);
                }
            }
            if(count($items) > 1){
                $actions['dropdown'] = $items;
            }else{
                $actions['icons'] = $items;
            }
            return $actions;
        });
        $rawColumns     = [];
        $rawColumns[]   = 'actions';
        $rawColumns[]   = 'languages';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function create(Request $request){
        $this->authorize('create', CrudModel::class);
        return view('cms::config.create', $this->data);
    }
    public function store(Request $request){
        $this->authorize('create', CrudModel::class);
        $rules = [
            'input_type'            => 'required|in:TEXT,TEXTAREA',
            'config_validate'       => 'required|in:nullable,required',
            'sort_order'            => 'nullable|digits_between:1,9|numeric|max:9999999999|min:1',
            'key'                   => ['required', 'string', new Slug, 'max:191', 'min:2', 'unique:cms_configs'],
            'val'                   => 'required|string|max:10000|min:2',
            'has_additional_info'   => 'required|in:0,1',
            'label_ar'              => 'required|string|max:255',
            'placeholder_ar'        => 'nullable|string|max:255',
            'help_ar'               => 'nullable|string|max:255',
            'icon_ar'               => 'nullable|string|max:255'
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!is_null($request->input('label_'.$locale))){
                    $rules['label_'.$locale]        = 'required|string|max:255';
                    $rules['placeholder_'.$locale]  = 'nullable|string|max:255';
                    $rules['help_'.$locale]         = 'nullable|string|max:255';
                    $rules['icon_'.$locale]         = 'nullable|string|max:255';
                }
            }
        }
        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);
        if($validator->fails()){
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
                $this->data['model']                        = new CrudModel;
                $this->data['model']->input_type            = $request->input_type;
                $this->data['model']->validations           = $request->config_validate;
                $this->data['model']->sort_order            = $request->sort_order;
                $this->data['model']->key                   = $request->key;
                $this->data['model']->val                   = $request->val;
                $this->data['model']->has_additional_info   = $request->has_additional_info;
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('label_'.$locale))){
                        $this->data['model']->{'label:'.$locale}        = $request->input('label_'.$locale);
                        $this->data['model']->{'placeholder:'.$locale}  = $request->input('placeholder_'.$locale);
                        $this->data['model']->{'help:'.$locale}         = $request->input('help_'.$locale);
                        $this->data['model']->{'icon:'.$locale}         = $request->input('icon_'.$locale);
                    }
                }
                $this->data['model']->save();
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
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }
    public function edit(Request $request){
        return back();
        $this->data['model']    = CrudModel::with('translations')->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        return view('cms::config.update', $this->data);
    }
    public function update(Request $request){
        $this->data['model']    = CrudModel::findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        $rules = [
            'input_type'            => 'required|in:TEXT,TEXTAREA',
            'config_validate'       => 'required|in:nullable,required',
            'sort_order'            => 'nullable|digits_between:1,9|numeric|max:9999999999|min:1',
            // 'key'                   => 'required|string|max:191|min:2|unique:cms_configs,key,' . $request->model,
            'val'                   => 'required|string|max:10000|min:2',
            'has_additional_info'   => 'required|in:0,1',
            'label_ar'              => 'required|string|max:255',
            'placeholder_ar'        => 'nullable|string|max:255',
            'help_ar'               => 'nullable|string|max:255',
            'icon_ar'               => 'nullable|string|max:255'
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!is_null($request->input('label_'.$locale))){
                    $rules['label_'.$locale]        = 'required|string|max:255';
                    $rules['placeholder_'.$locale]  = 'nullable|string|max:255';
                    $rules['help_'.$locale]         = 'nullable|string|max:255';
                    $rules['icon_'.$locale]         = 'nullable|string|max:255';
                }
            }
        }
        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);
        if($validator->fails()){
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
                $this->data['model']->input_type            = $request->input_type;
                $this->data['model']->validations           = $request->config_validate;
                $this->data['model']->sort_order            = $request->sort_order;
                // $this->data['model']->key                   = $request->key;
                $this->data['model']->val                   = $request->val;
                $this->data['model']->has_additional_info   = $request->has_additional_info;
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('label_'.$locale))){
                        $this->data['model']->{'label:'.$locale}        = $request->input('label_'.$locale);
                        $this->data['model']->{'placeholder:'.$locale}  = $request->input('placeholder_'.$locale);
                        $this->data['model']->{'help:'.$locale}         = $request->input('help_'.$locale);
                        $this->data['model']->{'icon:'.$locale}         = $request->input('icon_'.$locale);
                    }
                }
                $this->data['model']->save();
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
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }
    public function destroy(Request $request){
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);
        if(!$this->data['model']->trashed()){
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
        }else{
            $this->authorize('forceDelete', $this->data['model']);
            try {
                DB::transaction(function() use ($request) {
                    foreach($this->data['model']->translations as $trans){
                        if($trans->image) app()->ImageManipulator->deleteImage($trans->image);
                    }
                    $this->data['model']->forceDelete();
                });
            } catch (\Exception $e) {
                // dd($e->getMessage());
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
    public function restore(Request $request){
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);
        $this->authorize('restore', $this->data['model']);
        if(!$this->data['model']->trashed()){
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
    public function massDestroy(Request $request){
        $this->data['models'] = CrudModel::withTrashed()->whereIn('id', explode(',', $request->ids))->get();
        $this->data['attribute'] = 'title';
        $this->data['failed'] = collect([]);
        foreach($this->data['models'] as $model){
            if(!$model->trashed()){
                try {
                    $this->authorize('delete', $model);
                    DB::transaction(function() use ($request, $model) {
                        $model->delete();
                    });
                } catch (\Exception $e) {
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }
            }else{
                try {
                    $this->authorize('forceDelete', $model);
                    DB::transaction(function() use ($request, $model) {
                        foreach($model->translations as $trans){
                            if($trans->image) app()->ImageManipulator->deleteImage($trans->image);
                        }
                        $model->forceDelete();
                    });
                } catch (\Exception $e) {
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }

            }
        }
        if($this->data['failed']->count() > 0){
            $failed = '';
            foreach($this->data['failed'] as $item){
                $failed .= '<span class="kt-badge kt-badge--dark kt-badge--inline">' . $item . '</span> ';
            }
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.mass_delete_error.title'),
                'description' => __('cms::messages.mass_delete_error.description') . '<div class="mt-2">' . $failed . '</div>'
            ]);
        }else{
            return new ResponseHandler([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.mass_delete_success.title'),
                'description' => __('cms::messages.mass_delete_success.description')
            ]);
        }
    }
    public function massRestore(Request $request){
        $this->data['models'] = CrudModel::onlyTrashed()->whereIn('id', explode(',', $request->ids))->get();
        $this->data['attribute'] = 'title';
        $this->data['failed'] = collect([]);
        foreach($this->data['models'] as $model){
            try {
                $this->authorize('restore', $model);
                DB::transaction(function() use ($request, $model) {
                    $model->restore();
                });
            }catch (\Exception $e) {
                $this->data['failed']->push($model->{$this->data['attribute']});
            }
        }
        if($this->data['failed']->count() > 0){
            $failed = '';
            foreach($this->data['failed'] as $item){
                $failed .= '<span class="kt-badge kt-badge--dark kt-badge--inline">' . $item . '</span> ';
            }
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.mass_restore_error.title'),
                'description' => __('cms::messages.mass_restore_error.description') . '<div class="mt-2">' . $failed . '</div>'
            ]);
        }else{
            return new ResponseHandler([
                'success'     => true,
                'type'        => 'success',
                'title'       => __('cms::messages.mass_restore_success.title'),
                'description' => __('cms::messages.mass_restore_success.description')
            ]);
        }
    }
    public function editConfig(Request $request){
        $this->data['model']    = CrudModel::with('translations')->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        return view('cms::config.update_config', $this->data);
    }
    public function updateConfig(Request $request){
        $this->data['model']        = CrudModel::with('translations')->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        $this->data['keys']        = [];
        $this->data['validations'] = [];
        $tolower                               = strtolower($this->data['model']->key);
        $this->data['keys'][ $tolower ]        = $request->{ $tolower };
        if($this->data['model']->input_type == 'TEXTAREA'){
            $this->data['validations'][ $tolower ] = $this->data['model']->validations . '|string|max:10000';
            if($this->data['model']->has_additional_info == 1){
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    $this->data['validations'][ 'title_'.$locale ]          = 'required|string|max:255';
                    $this->data['validations'][ 'description_'.$locale ]    = 'nullable|string|max:10000';
                    $this->data['validations'][ 'image_'.$locale ]          = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
                    $this->data['keys'][ 'title_'.$locale ]                 = $request->input('title_'.$locale);
                    $this->data['keys'][ 'description_'.$locale ]           = $request->input('description_'.$locale);
                    $this->data['keys'][ 'image_'.$locale ]                 = $request->file('image_'.$locale);
                }
            }
        }elseif($this->data['model']->input_type == 'TEXT'){
            $this->data['validations'][ $tolower ] = $this->data['model']->validations . '|string|max:255';
            if($this->data['model']->has_additional_info == 1){
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    $this->data['validations'][ 'title_'.$locale ]          = 'required|string|max:255';
                    $this->data['validations'][ 'description_'.$locale ]    = 'nullable|string|max:10000';
                    $this->data['validations'][ 'image_'.$locale ]          = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
                    $this->data['keys'][ 'title_'.$locale ]                 = $request->input('title_'.$locale);
                    $this->data['keys'][ 'description_'.$locale ]           = $request->input('description_'.$locale);
                    $this->data['keys'][ 'image_'.$locale ]                 = $request->file('image_'.$locale);
                }
            }
        }
        if( ! empty( $this->data['validations'] ) ) {
            $validator = Validator::make($this->data['keys'], $this->data['validations'], [], $this->attributeNames);
        }
        if($validator->fails()){
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'validation_error',
                'title'         => __('cms::messages.validation_error.title'),
                'description'   => __('cms::messages.validation_error.description'),
                'errors'        => $validator->getMessageBag()->toArray()
            ], 422);
        }
        $img = [];
        try {
            DB::transaction(function() use ($request,$img,$tolower) {
                $this->data['model']->val = $request->{ $tolower } ;
                if ($this->data['model']->has_additional_info == '1') {
                    foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                        $this->data['model']->{'label:'.$locale}            = $request->input('label_'.$locale);
                        $this->data['model']->{'title:'.$locale}            = $request->input('title_'.$locale);
                        $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                        ${'imagePath'.$locale} = null;
                        if($request->hasFile('image_'.$locale)){
                            ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('config');
                            $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                        }
                    }
                }
                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
            }
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
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }
}
