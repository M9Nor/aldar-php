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
use Modules\Cms\Entities\Category as CrudModel;
use Modules\Cms\Entities\Content;
use App\Rules\Slug;
use Validator;
use Bouncer;
use Auth;
use DB;
use LaravelLocalization;

class CategoryController extends CmsController
{
    public $attributeNames = [];
    public function __construct(){
        $this->data['type'] = request('type');
        $this->attributeNames = [
            'slug'          => __('cms::cruds.categories.slug.label'),
            'type'          => __('cms::cruds.categories.type.label'),
            'parent_id'     => __('cms::cruds.categories.parent.label'),
            'sort_order'    => __('cms::cruds.categories.sort_order.label'),
            'icon_image'    => __('cms::cruds.categories.icon_image.label'),
            'tags'          => __('cms::cms::categories.fields.tags.label'),
            'tags.*'        => __('cms::cms::categories.fields.tags.label'),
        ];
        $translationAttributeNames = [];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $names = [
                'title_'.$locale        => __('cms::cruds.categories.title_'.$locale.''),
                'brief_'.$locale        => __('cms::cruds.categories.brief_'.$locale.''),
                'description_'.$locale  => __('cms::cruds.categories.description_'.$locale.''),
                'image_'.$locale        => __('cms::cruds.categories.image_'.$locale.''),
                'keywords_'.$locale        => __('cms::cruds.categories.keywords_'.$locale.''),
                'seo_description_'.$locale        => __('cms::cruds.categories.seo_description_'.$locale.''),
            ];
            $translationAttributeNames = array_merge($translationAttributeNames,$names);
        }
        $this->attributeNames = array_merge($this->attributeNames,$translationAttributeNames);
        $this->middleware('auth')->except([]);
        parent::__construct();
    }
    public function index(Request $request){
        CrudModel::typeAbort($this->data['type']);
        $this->authorize('view', [CrudModel::class, $this->data['type']]);
        return view('cms::categories.index', $this->data);
    }
    public function data(Request $request){
        // $list = CrudModel::orderBy('id','DESC')->with('translations');
        $this->authorize('view', [CrudModel::class, $this->data['type']]);
        $list = CrudModel::select(['cms_categories.*',
            DB::raw('
                (
                    SELECT trans.title
                    FROM cms_category_translations AS trans
                    WHERE trans.category_id = cms_categories.id
                    AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
            ')
        ])->where('type',$this->data['type'])->with('translations');
        $withTrashed = request('trashed', 'hide');
        $list->when($this->data['type'] == 'filters', function($query) {
            $query->whereNotNull('parent_id');
        })->when($withTrashed == 'show', function($query) {
            $query->onlyTrashed();
        });
        $allCategories = CrudModel::withTrashed()->get();
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
                })->when(!empty($filter['brief']), function($query) use ($filter) {
                    $query->whereTranslationLike('brief', "%{$filter['brief']}%");
                })->when(!empty($filter['description']), function($query) use ($filter) {
                    $query->whereTranslationLike('description', "%{$filter['description']}%");
                });
            }
        })
        ->addColumn('translated_name', function($model) {
            return !is_null( $model->new_title ) ? $model->new_title : $model->translations->first()->title;
        })->addColumn('languages', function($model){
            $output = '';
            foreach($model->translations->sortBy('locale') as $translation){

                if($translation->locale == 'ar') {
                    $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold" aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
                } else {
                    $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
                }

                if(auth()->user()->can('deleteTranslation', [$model,$this->data['type']]) && $translation->locale != 'ar')
                {
                    $output .= '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">
                        <a class="dropdown-item" href="javascript:;" onclick="deleteTranslationConfirmation(null, function(){ $(`#deleteTranslationForm'.$model->id.'`).submit(); })">
                            '.__('cms::global.actions.delete_translation').'
                        </a>
                        <form id="deleteTranslationForm'.$model->id.'" onsubmit="onFormSubmit(event);" method="POST" action="'.route('CategoryController@destroyTranslation', ['model' => $model->id, 'locale' => $translation->locale]).'" style="display: none;">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                        </form>
                    </div>';
                }
                $output .= '</div>';
            }
            return $output;
        })
        ->addColumn('parent_categories', function($model) use ($allCategories) {
            $result = '';
            foreach($model->AllCategoryParents($model,[],$allCategories) as $key => $parent){
                if(!$parent->last){
                    if($key!=0){
                        $result .= ' > ';
                    }
                }
                $result .=  str_replace(["&amp;"]," ",$parent->translateOrFirst(app()->getLocale())->title);
            }
            return $result;
        })
        ->addColumn('image', function($model){
            return [
                'src' => $model->translate() == NULL ? CrudModel::getImage(new CrudModel, '85x85', $this->data['type']) : CrudModel::getImage($model->translate(), '85x85', $this->data['type'])
            ];
        })
        ->addColumn('actions', function($model) {
            $items = [];
            $actions['dropdown'] = [];
            $actions['icons'] = [];
            if(!$model->trashed()){
                if(auth()->user()->can('update', [$model, $model->type])){
                    $items[] = array_merge($this->actions['edit'], [
                        'url'   => route('CategoryController@edit', ['model' => $model->id,'type' => $this->data['type']]),
                        'id'    => 'edit_' . $model->id
                    ]);
                }
                if(auth()->user()->can('delete', [$model, $model->type])){
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('CategoryController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
            }
            if($model->trashed()){
                if(auth()->user()->can('restore', [$model, $model->type])){
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('CategoryController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', [$model, $model->type])){
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('CategoryController@destroy', ['model' => $model->id]),
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
        CrudModel::typeAbort($this->data['type']);
        $this->authorize('create', [CrudModel::class, $this->data['type']]);
        return view('cms::categories.create', $this->data);
    }
    public function store(Request $request){
        $this->authorize('create', [CrudModel::class, $this->data['type']]);
        $rules = [
            'parent_id'         => (CrudModel::isFieldRequired($this->data['type'], 'parent') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'sort_order'        => (CrudModel::isFieldRequired($this->data['type'], 'sort_order') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'slug'              => [(CrudModel::isFieldRequired($this->data['type'], 'slug') ? 'required' : 'nullable'), 'string', new Slug, 'max:191', 'min:3', 'unique:cms_categories,slug'],
            'title_ar'          => (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'required').'|string|max:191',
            'brief_ar'          => (CrudModel::isFieldRequired($this->data['type'], 'brief') ? 'required' : 'nullable').'|string|max:500',
            'description_ar'    => (CrudModel::isFieldRequired($this->data['type'], 'description') ? 'required' : 'nullable').'|string|max:5000',
            'image_ar'          => (CrudModel::isFieldRequired($this->data['type'], 'image') ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1920',
            'icon_image'        => (CrudModel::isFieldRequired($this->data['type'], 'icon_image') ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1920',
            'tags_ar'           => (CrudModel::isFieldRequired($this->data['type'], 'tags') ? 'required' : 'nullable').'|array',
            'tags_ar.*'         => (CrudModel::isFieldRequired($this->data['type'], 'tags') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                // if(!empty($request->input('title_'.$locale))){
                    $rules['title_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'required').'|string|max:191';
                    $rules['brief_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'brief') ? 'required' : 'nullable').'|string|max:500';
                    $rules['description_'.$locale]      = (CrudModel::isFieldRequired($this->data['type'], 'description') ? 'required' : 'nullable').'|string|max:5000';
                    $rules['image_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'image') ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1920';
                    $rules['seo_description_'.$locale]  = (CrudModel::isFieldRequired($this->data['type'], 'seo_description') ? 'required' : 'nullable').'|string|max:5000';
                    $rules['keywords_'.$locale]         = (CrudModel::isFieldRequired($this->data['type'], 'keywords') ? 'required' : 'nullable').'|string|max:5000';
                    $rules['tags_'.$locale]             = 'nullable|array';
                    $rules['tags_'.$locale.'.*']        = 'nullable|digits_between:1,9|numeric|max:9999999999|min:1';
                // }
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
                $this->data['model']                    = new CrudModel;
                $this->data['model']->type              = $this->data['type'];
                $this->data['model']->parent_id         = $request->parent_id;
                $this->data['model']->sort_order        = $request->sort_order;
                $this->data['model']->slug        = $request->slug;
                $imageIconPath = null;
                if($request->hasFile('icon_image')){
                    $imageIconPath                      = $request->file('icon_image')->store($this->data['type']);
                    $this->data['model']->icon_image    = $imageIconPath;
                }
                $tags = collect();
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!empty($request->input('title_'.$locale))){
                        $this->data['model']->{'title:'.$locale}            = $request->input('title_'.$locale);
                        $this->data['model']->{'brief:'.$locale}            = $request->input('brief_'.$locale);
                        $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                        $this->data['model']->{'seo_description:'.$locale}  = $request->input('seo_description_'.$locale);
                        $this->data['model']->{'keywords:'.$locale}         = $request->input('keywords_'.$locale);
                        ${'imagePath'.$locale} = null;
                        if($request->hasFile('image_'.$locale)){
                            ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store($this->data['type']);
                            $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                        }
                        $tags   = $tags->merge($request->{'tags_'.$locale});
                    }
                }
                $this->data['model']->save();
                $this->data['model']->tags()->sync($tags->unique());
                // if (is_array($request->tags) && !empty($request->tags)){
                //     $this->data['model']->tags()->sync($request->tags);
                // }
            });
        } catch (\Exception $e) {
            if(array_key_exists('imageIconPath', $this->data)) app()->ImageManipulator->deleteImage($imageIconPath);
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
            }
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
            // 'redirect_url'  => route('CategoryController@index'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }
    public function edit(Request $request){
        CrudModel::typeAbort($this->data['type']);
        $this->data['model']    = CrudModel::where('type',$this->data['type'])->with('translations','tags.translations')->findOrFail($request->model);
        $this->authorize('update',[$this->data['model'], $this->data['type']]);
        $this->data['allParentCategory'] = $this->data['model']->AllCategoryParents($this->data['model']);
        if (is_array($this->data['allParentCategory'])){
            $ids = array_column($this->data['allParentCategory'], 'id') ;
            $this->data['allParentCategory'] = CrudModel::with('translations')->whereIntegerInRaw('id', $ids)->get();
        }else{
            $this->data['allParentCategory'] = collect();
        }
        return view('cms::categories.update', $this->data);
    }
    public function update(Request $request){
        $this->data['model']    = CrudModel::where('type',$this->data['type'])->findOrFail($request->model);

        $this->authorize('update',[$this->data['model'], $this->data['type']]);
        $rules = [
            'parent_id'         => (CrudModel::isFieldRequired($this->data['type'], 'parent') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'sort_order'        => (CrudModel::isFieldRequired($this->data['type'], 'sort_order') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'slug'              => [(CrudModel::isFieldRequired($this->data['type'], 'slug') ? 'required' : 'nullable'), 'string', new Slug, 'max:191', 'min:3', 'unique:cms_categories,slug,'.$request->model],
            'title_ar'          => (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'required').'|string|max:191',
            'brief_ar'          => (CrudModel::isFieldRequired($this->data['type'], 'brief') ? 'required' : 'nullable').'|string|max:500',
            'description_ar'    => (CrudModel::isFieldRequired($this->data['type'], 'description') ? 'required' : 'nullable').'|string|max:5000',
            'image_ar'          => ((CrudModel::isFieldRequired($this->data['type'], 'image') && empty($this->data['model']->translateOrFirst()->image)) ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1920',
            'icon_image'        => ((CrudModel::isFieldRequired($this->data['type'], 'icon_image') && empty($this->data['model']->icon_image)) ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1920',
            'tags_ar'           => 'nullable|array',
            'tags_ar.*'         => 'nullable|digits_between:1,9|numeric|max:9999999999|min:1',
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                // if(!is_null($request->input('title_'.$locale))){
                    $rules['title_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'required').'|string|max:191';
                    $rules['brief_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'brief') ? 'required' : 'nullable').'|string|max:500';
                    $rules['description_'.$locale]      = (CrudModel::isFieldRequired($this->data['type'], 'description') ? 'required' : 'nullable').'|string|max:5000';
                    $rules['image_'.$locale]            = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1920';
                    $rules['keywords_'.$locale]         = (CrudModel::isFieldRequired($this->data['type'], 'keywords') ? 'required' : 'nullable').'|string|max:5000';
                    $rules['seo_description_'.$locale]  = (CrudModel::isFieldRequired($this->data['type'], 'seo_description') ? 'required' : 'nullable').'|string|max:5000';
                    $rules['tags_'.$locale]             = 'nullable|array';
                    $rules['tags_'.$locale.'.*']        = 'nullable|digits_between:1,9|numeric|max:9999999999|min:1';
                // }
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
                $this->data['model']->slug                              = $request->slug;
                if(!empty($request->parent_id)){
                    $this->data['model']->parent_id                         = $request->parent_id;
                }
                $this->data['model']->sort_order                        = $request->sort_order;
                $imageIconPath = null;
                if($request->hasFile('icon_image')){
                    $imageIconPath                      = $request->file('icon_image')->store($this->data['type']);
                    $this->data['model']->icon_image    = $imageIconPath;
                }
                $tags = collect();
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!empty($request->input('title_'.$locale))){
                        $this->data['model']->{'title:'.$locale}            = $request->input('title_'.$locale);
                        $this->data['model']->{'brief:'.$locale}            = $request->input('brief_'.$locale);
                        $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                        $this->data['model']->{'seo_description:'.$locale}  = $request->input('seo_description_'.$locale);
                        $this->data['model']->{'keywords:'.$locale}         = $request->input('keywords_'.$locale);
                        ${'imagePath'.$locale} = null;
                        if($request->hasFile('image_'.$locale)){
                            ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store($this->data['type']);
                            $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                        }
                        $tags = $tags->merge($request->{'tags_'.$locale});
                    }
                }
                $this->data['model']->save();
                $this->data['model']->tags()->sync($tags->unique());
                // if (is_array($request->tags) && !empty($request->tags)) {
                //     $this->data['model']->tags()->sync($request->tags);
                // }
            });
        } catch (\Exception $e) {
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
            }
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
    public function getCategoriesSelect2(Request $request){
        $term = trim((string) $request->search);
        $page = $request->page;
        if(!$page){
            $page = 1;
        }
        $list = CrudModel::select('cms_categories.*');
        if($request->type != 'all'){
            if($request->type == 'filters_cat'){
                $list = $list->where('type','real_estate')->orWhere('type','turkey_real_estate')->orWhere('type','istanbul_real_estate');
            }else if($request->type == 'filters'){
                $list = $list->where('type',$request->type)->whereNotNull('parent_id');
            }else if($request->type == 'filter_categories'){
                $list = $list->where('type', 'filters')->whereNull('parent_id');
            }else{
                $list = $list->where('type',$request->type);
            }
        }
        $list->when($request->additional_params, function($query, $additional_params) {
            if(array_key_exists('is_parent', $additional_params) && !is_null($additional_params['is_parent'])){
                $query->whereNull('parent_id');
            }
            if(array_key_exists('category_parent_id', $additional_params) && !is_null($additional_params['category_parent_id'])){
                $query->where('parent_id',$additional_params['category_parent_id']);
            }
        });
        $list = $list->when($request->model, function($query, $model) {
                $query->where('id', '!=', $model);
            })->where(function($q) use ($term) {
                $q->whereTranslationLike('title', "%{$term}%");
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
    public function getChilds(Request $request){
        $categories = CrudModel::with('translations')->whereIn('id',$request->category_parent_id)->get();
        return response()->json([
            'success'               => true,
            'categories'            => $categories,
        ]);
    }
    public function destroy(Request $request){
        $this->data['model'] = CrudModel::withTrashed()->findOrFail($request->model);
        // Check if the authenticated user is allowed to proceed farther.
        if(!$this->data['model']->trashed()){
            $this->authorize('delete', [$this->data['model'],$this->data['type']]);
            try {
                DB::transaction(function() use ($request) {
                    $category_childs = $this->data['model']->GetAllChilds($this->data['model']);
                    if(!empty($category_childs)){
                        foreach($category_childs as $child){
                            $child->delete();
                        }
                    }
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
            // Check if the authenticated user is allowed to proceed farther.
            $this->authorize('forceDelete', [$this->data['model'],$this->data['type']]);
            try {
                DB::transaction(function() use ($request) {
                    $category_childs = $this->data['model']->GetAllChildsWithTrashed($this->data['model']);
                    if(!empty($category_childs)){
                        foreach($category_childs as $child){
                            foreach ($child->translations as $key => $chidl_trans){
                                if($chidl_trans->image) app()->ImageManipulator->deleteImage($chidl_trans->image);
                            }
                            $child->forceDelete();
                        }
                    }
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
    public function destroyTranslation(Request $request){
        $this->data['model'] = CrudModel::withDisabled()->withTrashed()->with('translations')->findOrFail($request->model);
        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('deleteTranslation',[$this->data['model'],$this->data['type']]);
        if($this->data['model']->translations()->count() > 1){
            try {
                DB::transaction(function() use ($request) {
                    $this->data['model']->translations()->where('locale', $request->locale)->delete();
                });
            } catch (\Exception $e) {
                return new ResponseHandler([
                    'success'     => false,
                    'type'        => 'danger',
                    'title'       => __('cms::messages.translation_delete_error.title'),
                    'description' => __('cms::messages.translation_delete_error.description')
                ]);
            }
        }
        else
        {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'warning',
                'title'       => __('cms::messages.cannot_delete_last_translation.title'),
                'description' => __('cms::messages.cannot_delete_last_translation.description')
            ]);
        }

        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.translation_delete_success.title'),
            'description' => __('cms::messages.translation_delete_success.description')
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
                $allCategories = CrudModel::withTrashed()->get();
                foreach($this->data['model']->AllCategoryParents($this->data['model'],[],$allCategories) as $key => $parent){
                    $parent->restore();
                }
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
        // The model attribute which will be shown to user to indicate the unsuccessful models.
        $this->data['attribute'] = 'title';
        $this->data['failed'] = collect([]);
        // Loop through the selected models to determine which of whom can be deleted.
        foreach($this->data['models'] as $model){
            if(!$model->trashed()){
                try {
                    // Check if the authenticated user is allowed to proceed farther.
                    $this->authorize('delete', [$model,$this->data['type']]);
                    DB::transaction(function() use ($request, $model) {
                        $category_childs = $model->GetAllChilds($model);
                        if(!empty($category_childs)){
                            foreach($category_childs as $child){
                                $child->delete();
                            }
                        }
                        $model->delete();
                    });

                } catch (\Exception $e) {
                    // Push failed models to failed array to notify the user which models could not be deleted.
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }
            }else{
                try {
                    // Check if the authenticated user is allowed to proceed farther.
                    $this->authorize('forceDelete',[$model,$this->data['type']]);
                    DB::transaction(function() use ($request, $model) {
                        $category_childs = $model->GetAllChildsWithTrashed($model);
                        if(!empty($category_childs)){
                            foreach($category_childs as $child){
                                foreach ($child->translations as $key => $chidl_trans){
                                    if($chidl_trans->image) app()->ImageManipulator->deleteImage($chidl_trans->image);
                                }
                                $child->forceDelete();
                            }
                        }
                        foreach($model->translations as $trans){
                            if($trans->image) app()->ImageManipulator->deleteImage($trans->image);
                        }
                        $model->forceDelete();
                    });
                } catch (\Exception $e) {
                    // Push failed models to failed array to notify the user which models could not be deleted.
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
        // The model attribute which will be shown to user to indicate the unsuccessful models.
        $this->data['attribute'] = 'title';
        $this->data['failed'] = collect([]);
        // Loop through the selected models to determine which of whom can be restored.
        foreach($this->data['models'] as $model){
            try {
                // Check if the authenticated user is allowed to proceed farther.
                $this->authorize('restore',[$model,$this->data['type']]);
                DB::transaction(function() use ($request, $model) {
                    $allCategories = CrudModel::withTrashed()->get();
                    foreach($model->AllCategoryParents($model,[],$allCategories) as $key => $parent){
                        $parent->restore();
                    }
                    $model->restore();
                });
            }catch (\Exception $e) {
                // Push failed models to failed array to notify the user which models could not be restored.
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
}
