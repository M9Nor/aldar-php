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
use Modules\Cms\Entities\Attachment;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\ExternalAttachments;
use Modules\Cms\Entities\Content as CrudModel;
use Modules\Cms\Entities\Attributes;
use App\Rules\Slug;
use Validator;
use Bouncer;
use Auth;
use DB;
use LaravelLocalization;

class ContentController extends CmsController
{
    public $attributeNames = [];

    public function __construct(){
        if(request()->type)
        {
            $this->data['type'] = request('type');
            if(! CrudModel::typeExists($this->data['type']))
            {
                abort(404);
            }
        }
        $this->attributeNames = [
            'link'                                  => __('cms::cruds.contents.link.label'),
            'currency_value'                        => __('cms::cruds.contents.currency_value.label'),
            'currency_code'                         => __('cms::cruds.contents.currency_code.label'),
            'currency_symbol'                       => __('cms::cruds.contents.currency_symbol.label'),
            'currency_icon'                         => __('cms::cruds.contents.currency_icon.label'),
            'tags.*'                                => __('cms::cruds.contents.tags.label'),
            'tags'                                  => __('cms::cruds.contents.tags.label'),
            'slug'                                  => __('cms::cruds.contents.slug.label'),
            'sort_order'                            => __('cms::cruds.contents.sort_order.label'),
            'views'                                 => __('cms::cruds.contents.views.label'),
            'categories_ids'                        => __('cms::cruds.contents.categories.label'),
            'external_attachments.*.att_type'       => __('cms::cruds.contents.external_attachments.type.label'),
            'external_attachments.*.att_name'       => __('cms::cruds.contents.external_attachments.name.label'),
            'external_attachments.*.att_description'=> __('cms::cruds.contents.external_attachments.description.label'),
            'external_attachments.*.att_link'       => __('cms::cruds.contents.external_attachments.link.label'),
            'external_attachments_new.*.type'       => __('cms::cruds.contents.external_attachments.type.label'),
            'external_attachments_new.*.name'       => __('cms::cruds.contents.external_attachments.name.label'),
            'external_attachments_new.*.description'=> __('cms::cruds.contents.external_attachments.description.label'),
            'external_attachments_new.*.link'       => __('cms::cruds.contents.external_attachments.link.label'),
        ];
        if(CrudModel::getTypeCustomFields(request('type'),'custom_fields')){
            foreach(CrudModel::getTypeCustomFields(request('type'),'custom_fields') as $key => $custom_field){
                $this->attributeNames = [
                    $key            => __('backend::contents.custom_fields.'.$key.'.label'),
                ];
            }
        }
        $translationAttributeNames = [];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $names = [
                'title_'.$locale            => __('cms::cruds.contents.title_'.$locale.''),
                'image_'.$locale            => __('cms::cruds.contents.image_'.$locale.''),
                'brief_'.$locale            => __('cms::cruds.contents.brief_'.$locale.''),
                'link_trans_'.$locale       => __('cms::cruds.contents.link_trans_'.$locale.''),
                'description_'.$locale      => __('cms::cruds.contents.description_'.$locale.''),
                'seo_description_'.$locale  => __('cms::cruds.contents.seo_description_'.$locale.''),
                'about_'.$locale            => __('cms::cruds.contents.about_'.$locale.''),
                'keywords_'.$locale         => __('cms::cruds.contents.keywords_'.$locale.''),
            ];
            $translationAttributeNames = array_merge($translationAttributeNames,$names);
        }
        $this->attributeNames = array_merge($this->attributeNames,$translationAttributeNames);
        $this->middleware('auth')->except([]);
        parent::__construct();
    }
    public function index(Request $request)
    {
        $this->authorize('view', [CrudModel::class, $this->data['type']]);
        return view('cms::contents.index', $this->data);
    }
    public function data(Request $request)
    {
        $this->authorize('view', [CrudModel::class, $this->data['type']]);
        $list = CrudModel::select(['cms_contents.*',
            DB::raw('
                (
                    SELECT trans.title
                    FROM cms_content_translations AS trans
                    WHERE trans.content_id = cms_contents.id
                    AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
            ')
        ])->where('type',$this->data['type'])->with('translations')->withDisabled()->orderBy('id','DESC');

        $withTrashed = request('trashed', 'hide');
        $list->where('type',$this->data['type'])->when($withTrashed == 'show', function($query) {
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
                $q->when(!empty($filter['type']), function($query) use ($filter) {
                    $query->where('type', 'like', "%{$filter['type']}%");
                })->when(!empty($filter['title']), function($query) use ($filter) {
                    $query->whereTranslationLike('title', "%{$filter['title']}%");
                })->when(!empty($filter['brief']), function($query) use ($filter) {
                    $query->whereTranslationLike('brief', "%{$filter['brief']}%");
                })->when(!empty($filter['description']), function($query) use ($filter) {
                    $query->whereTranslationLike('description', "%{$filter['description']}%");
                });
            }
        })
        ->addColumn('image', function($model){
            return [
                'src' => $model->translateOrFirst() == NULL ? CrudModel::getImage(new CrudModel, '85x85', $this->data['type']) : CrudModel::getImage($model->translateOrFirst(), '85x85', $this->data['type'])
            ];
        })
        ->addColumn('translated_name', function($model) {
            return !is_null( $model->new_title ) ? $model->new_title : $model->translations->first()->title;
        })->addColumn('languages', function($model){
            $output = '';

            foreach($model->translations->sortBy('locale') as $translation)
            {
                if($translation->locale == 'ar') {
                    $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold" aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
                } else if(auth()->user()->can('deleteTranslation', [$model,$this->data['type']]) && $translation->locale != 'ar')
                {
                    $output .= '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">
                        <a class="dropdown-item" href="javascript:;" onclick="deleteTranslationConfirmation(null, function(){ $(`#deleteTranslationForm'.$model->id.'`).submit(); })">
                            '.__('cms::global.actions.delete_translation').'
                        </a>
                        <form id="deleteTranslationForm'.$model->id.'" onsubmit="onFormSubmit(event);" method="POST" action="'.route('ContentController@destroyTranslation', ['model' => $model->id, 'locale' => $translation->locale]).'" style="display: none;">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                        </form>
                    </div>';
                }
                $output .= '</div>';
            }
            return $output;
        })
        ->addColumn('actions', function($model) {
            $items = [];
            $actions['dropdown'] = [];
            $actions['icons'] = [];

            if(!$model->trashed()){
                if($model->type == 'currencies' && $model->currency_code == 'TRY'){
                    if(auth()->user()->can('update', [$model, $model->type])){
                        $items[] = array_merge($this->actions['edit'], [
                            'url'   => route('ContentController@edit', ['model' => $model->id , 'type' => $model->type]),
                            'id'    => 'edit_' . $model->id
                        ]);
                    }
                    if(is_null($model->disabled_at)){

                    }else{
                        if(auth()->user()->can('enable', [$model, $model->type])){
                            $items[] = array_merge($this->actions['enable'], [
                                'url'   => route('ContentController@enable', ['model' => $model->id]),
                                'id'    => 'enable_' . $model->id
                            ]);
                        }
                    }
                }else{
                    if(auth()->user()->can('update', [$model, $model->type])){
                        $items[] = array_merge($this->actions['edit'], [
                            'url'   => route('ContentController@edit', ['model' => $model->id , 'type' => $model->type]),
                            'id'    => 'edit_' . $model->id
                        ]);
                    }
                    if(auth()->user()->can('delete', [$model, $model->type])){
                        $items[] = array_merge($this->actions['delete'], [
                            'url'   => route('ContentController@destroy', ['model' => $model->id]),
                            'id'    => 'delete_' . $model->id
                        ]);
                    }
                    if(is_null($model->disabled_at)){
                        if(auth()->user()->can('disable', [$model, $model->type])){
                            $items[] = array_merge($this->actions['disable'], [
                                'url'   => route('ContentController@disable', ['model' => $model->id]),
                                'id'    => 'disable_' . $model->id
                            ]);
                        }
                    }else{
                        if(auth()->user()->can('enable', [$model, $model->type])){
                            $items[] = array_merge($this->actions['enable'], [
                                'url'   => route('ContentController@enable', ['model' => $model->id]),
                                'id'    => 'enable_' . $model->id
                            ]);
                        }
                    }
                }

            }
            if($model->trashed()){
                if($model->type == 'currencies' && $model->currency_code == 'TRY'){

                }else{
                    if(auth()->user()->can('restore', [$model, $model->type])){
                        $items[] = array_merge($this->actions['restore'], [
                            'url'   => route('ContentController@restore', ['model' => $model->id]),
                            'id'    => 'restore_' . $model->id
                        ]);
                    }
                    if(auth()->user()->can('forceDelete', [$model, $model->type])){
                        $items[] = array_merge($this->actions['force_delete'], [
                            'url'   => route('ContentController@destroy', ['model' => $model->id]),
                            'id'    => 'force_delete_' . $model->id
                        ]);
                    }
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
    public function create(Request $request)
    {
        $this->authorize('create', [CrudModel::class,$this->data['type']]);
        return view('cms::contents.create', $this->data);
    }
    public function store(Request $request)
    {
        $this->authorize('create', [CrudModel::class,$this->data['type']]);
        $rules = [
            'slug'                  => [(CrudModel::isFieldRequired($this->data['type'], 'slug') ? 'required' : 'nullable'), 'string', new Slug, 'max:191', 'min:3','unique:cms_contents'],
            'categories_ids'        => (CrudModel::isFieldRequired($this->data['type'], 'categories') ? 'required' : 'nullable').'|array',
            'categories_ids.*'      => (CrudModel::isFieldRequired($this->data['type'], 'categories') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'sort_order'            => (CrudModel::isFieldRequired($this->data['type'], 'sort_order') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'views'                 => (CrudModel::isFieldRequired($this->data['type'], 'views') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'link'                  => (CrudModel::isFieldRequired($this->data['type'], 'link') ? 'required' : 'nullable').'|string|url|max:191|min:1',
            'currency_value'        => (CrudModel::isFieldRequired($this->data['type'], 'currency_value') ? 'required' : 'nullable').'|numeric|max:9999999999|min:0',
            'currency_code'         => (CrudModel::isFieldRequired($this->data['type'], 'currency_code') ? 'required' : 'nullable').'|string|max:191|min:1',
            'currency_symbol'       => (CrudModel::isFieldRequired($this->data['type'], 'currency_symbol') ? 'required' : 'nullable').'|string|max:191|min:1',
            'currency_icon'         => (CrudModel::isFieldRequired($this->data['type'], 'currency_icon') ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=5,min_height=5,max_width=1920,max_height:1080',
            'attachments'           => (CrudModel::isFieldRequired($this->data['type'], 'attachments') ? 'required' : 'nullable').'|array',
            'attachments.*'         => (CrudModel::isFieldRequired($this->data['type'], 'attachments') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'tags_ar'               => (CrudModel::isFieldRequired($this->data['type'], 'tags') ? 'required' : 'nullable').'|array',
            'tags_ar.*'             => (CrudModel::isFieldRequired($this->data['type'], 'tags') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'title_ar'              => (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'required').'|string|max:191',
            'brief_ar'              => (CrudModel::isFieldRequired($this->data['type'], 'brief') ? 'required' : 'nullable').'|string|max:500',
            'link_trans_ar'         => (CrudModel::isFieldRequired($this->data['type'], 'link_trans') ? 'required' : 'nullable').'|string|max:255',
            'description_ar'        => (CrudModel::isFieldRequired($this->data['type'], 'description') ? 'required' : 'nullable').'|string|max:30000',
            'image_ar'              => (CrudModel::isFieldRequired($this->data['type'], 'image') ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080',
            'seo_description_ar'    => (CrudModel::isFieldRequired($this->data['type'], 'seo_description') ? 'required' : 'nullable').'|string|max:30000',
            'about_ar'              => (CrudModel::isFieldRequired($this->data['type'], 'about') ? 'required' : 'nullable').'|string|max:30000',

        ];
        if(CrudModel::getTypeCustomFields($this->data['type'],'custom_fields')){
            foreach(CrudModel::getTypeCustomFields($this->data['type'],'custom_fields') as $key => $custom_field){
                $rules[$key] = $custom_field['required'] == true ? 'required' : 'nullable'.'|string|max:191';
            }
        }
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            // dd($request->input('description_'.$locale));
            if($locale != 'ar'){
                if(!is_null($request->input('title_'.$locale))){
                    $rules['title_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'nullable').'|string|max:191';
                    $rules['brief_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'brief') ? 'required' : 'nullable').'|string|max:500';
                    $rules['link_trans_'.$locale]       = (CrudModel::isFieldRequired($this->data['type'], 'link_trans') ? 'required' : 'nullable').'|string|max:255';
                    $rules['description_'.$locale]      = (CrudModel::isFieldRequired($this->data['type'], 'description') ? 'required' : 'nullable').'|string|max:30000';
                    $rules['image_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'image') ? 'nullable' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
                    $rules['seo_description_'.$locale]  = (CrudModel::isFieldRequired($this->data['type'], 'seo_description') ? 'required' : 'nullable').'|string|max:30000';
                    $rules['about_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'about') ? 'required' : 'nullable').'|string|max:30000';
                    $rules['keywords_'.$locale]         = (CrudModel::isFieldRequired($this->data['type'], 'keywords') ? 'required' : 'nullable').'|string|max:30000';
                    $rules['tags_'.$locale]             = 'nullable|array';
                    $rules['tags_'.$locale.'.*']        = 'nullable|digits_between:1,9|numeric|max:9999999999|min:1';
                }
            }
        }
        if($request->external_attachments){
            $rules['external_attachments.*.type']           = 'required|string|max:191';
            $rules['external_attachments.*.name']           = 'required|string|max:191';
            $rules['external_attachments.*.description']    = 'nullable|string|max:191';
            $rules['external_attachments.*.link']           = 'required|string|max:191';
        }
        // dd($rules);
        // $checkLanguages = 0;
        // foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
        //     if(is_null($request->input('title_'.$locale))){
        //         $checkLanguages++;
        //     }
        // }
        // if(count(LaravelLocalization::getSupportedLocales()) == $checkLanguages){
        //     $rules['title_ar']        = 'required|string|max:191';
        // }
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
                $this->data['model']->link              = $request->link;
                $this->data['model']->sort_order        = $request->sort_order;
                $this->data['model']->views             = $request->views;
                $this->data['model']->slug              = $request->slug;
                $this->data['model']->currency_value    = $request->currency_value;
                $this->data['model']->currency_code     = $request->currency_code;
                $this->data['model']->currency_symbol   = $request->currency_symbol;
                $imageIconPath = null;
                if($request->hasFile('currency_icon')){
                    $imageIconPath                      = $request->file('currency_icon')->store($this->data['type']);
                    $this->data['model']->currency_icon = $imageIconPath;
                }
                $tags = collect();
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('title_'.$locale))){
                        $this->data['model']->{'title:'.$locale}            = $request->input('title_'.$locale);
                        $this->data['model']->{'brief:'.$locale}            = $request->input('brief_'.$locale);
                        $this->data['model']->{'link_trans:'.$locale}       = $request->input('link_trans_'.$locale);
                        $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                        $this->data['model']->{'seo_description:'.$locale}  = $request->input('seo_description_'.$locale);
                        $this->data['model']->{'about:'.$locale}            = $request->input('about_'.$locale);
                        $this->data['model']->{'keywords:'.$locale}         = $request->input('keywords_'.$locale);
                        ${'imagePath'.$locale} = null;
                        if($request->hasFile('image_'.$locale)){
                            ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store($this->data['type']);
                            $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                        }
                        // if (is_array($request->{'tags_'.$locale}) && !empty($request->{'tags_'.$locale})) {
                        //     $this->data['model']->tags()->attach($request->{'tags_'.$locale});
                        // }
                        $tags   = $tags->merge($request->{'tags_'.$locale});
                    }
                }
                $this->data['model']->save();
                $this->data['model']->tags()->sync($tags->unique());
                // if (is_array($request->tags) && !empty($request->tags)){
                //     $this->data['model']->tags()->attach($request->tags);
                // }
                if(CrudModel::getTypeCustomFields($this->data['type'],'custom_fields')){
                    $attributesArray = [];
                    foreach(CrudModel::getTypeCustomFields($this->data['type'],'custom_fields') as $key => $custom_field){
                        $attributesArray[] = [
                            'content_id'        => $this->data['model']->id,
                            'type'              => $custom_field['type'],
                            'key'               => $key,
                            'value'             => $request->input($key),
                        ];
                    }
                    if(!empty($attributesArray)){
                        Attributes::insert($attributesArray);
                    }
                }
                if (is_array($request->categories_ids)) {
                    $this->data['model']->categories()->sync($request->categories_ids);
                }
                // if (is_array($request->attachments) && !empty($request->attachments)) {
                //     Attachment::whereIn('id', $request->attachments)->update([
                //         'type'              => 'LINKED',
                //         'attachable_id'     => $this->data['model']->id,
                //         'attachable_type'   => get_class($this->data['model']),
                //     ]);
                // }
                if (is_array($request->attachments) && !empty($request->attachments)) {
                    $attachments = Attachment::whereIn('id', $request->attachments)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if($request->external_attachments){
                    $this->data['externalAttachments'] = [];
                    foreach ($request->external_attachments as $key => $externalAttachment) {
                        $this->data['externalAttachments'][] = [
                            'attachable_type'   => CrudModel::class,
                            'attachable_id'     => $this->data['model']->id,
                            'type'              => $externalAttachment['type'],
                            'name'              => $externalAttachment['name'],
                            'description'       => $externalAttachment['description'],
                            'link'              => $externalAttachment['link'],
                        ];
                    }
                    if (!empty($this->data['externalAttachments'])) {
                        ExternalAttachments::insert($this->data['externalAttachments']);
                    }
                }
            });
        } catch (\Exception $e) {
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
            }
            if(array_key_exists('imageIconPath', $this->data)) app()->ImageManipulator->deleteImage($imageIconPath);
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
            'redirect_url'  => request('redirect_url', 'javascript:;'),
            'model'         => [
                'model_id'      => $this->data['model']->id,
                'model_type'    => get_class($this->data['model'])
            ]
        ]);
    }
    public function edit(Request $request)
    {
        $this->data['model']        = CrudModel::withDisabled()->with('translations','externalAttachments','tags.translations')->where('type',$this->data['type'])->findOrFail($request->model);
        $this->authorize('update', [$this->data['model'],$this->data['model']->type]);
        return view('cms::contents.update', $this->data);
    }
    public function update(Request $request)
    {
        $this->data['model']        = CrudModel::with('translations','tags')->withDisabled()->where('type',$this->data['type'])->findOrFail($request->model);
        $this->authorize('update', [$this->data['model'],$this->data['type']]);
        $rules = [
            'slug'                  => [(CrudModel::isFieldRequired($this->data['type'], 'slug') ? 'required' : 'nullable'), 'string', new Slug, 'max:191', 'min:3','unique:cms_contents,slug,'.$this->data['model']->id.''],
            'categories_ids'        => (CrudModel::isFieldRequired($this->data['type'], 'categories') ? 'required' : 'nullable').'|array',
            'categories_ids.*'      => (CrudModel::isFieldRequired($this->data['type'], 'categories') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'sort_order'            => (CrudModel::isFieldRequired($this->data['type'], 'sort_order') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'views'                 => (CrudModel::isFieldRequired($this->data['type'], 'views') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'link'                  => (CrudModel::isFieldRequired($this->data['type'], 'link') ? 'required' : 'nullable').'|string|url|max:191|min:1',
            'currency_value'        => (CrudModel::isFieldRequired($this->data['type'], 'currency_value') ? 'required' : 'nullable').'|numeric|max:9999999999|min:0',
            'currency_code'         => (CrudModel::isFieldRequired($this->data['type'], 'currency_code') ? 'nullable' : 'nullable').'|string|max:191|min:1',
            'currency_symbol'       => (CrudModel::isFieldRequired($this->data['type'], 'currency_symbol') ? 'required' : 'nullable').'|string|max:191|min:1',
            'currency_icon'         => (CrudModel::isFieldRequired($this->data['type'], 'currency_icon') ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=5,min_height=5,max_width=1920,max_height:1080',
            'attachments'           => (CrudModel::isFieldRequired($this->data['type'], 'attachments') ? 'required' : 'nullable').'|array',
            'attachments.*'         => (CrudModel::isFieldRequired($this->data['type'], 'attachments') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'tags_ar'               => (CrudModel::isFieldRequired($this->data['type'], 'tags') ? 'required' : 'nullable').'|array',
            'tags_ar.*'             => (CrudModel::isFieldRequired($this->data['type'], 'tags') ? 'required' : 'nullable').'|digits_between:1,9|numeric|max:9999999999|min:1',
            'title_ar'              => (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'required').'|string|max:191',
            'brief_ar'              => (CrudModel::isFieldRequired($this->data['type'], 'brief') ? 'required' : 'nullable').'|string|max:500',
            'link_trans_ar'         => (CrudModel::isFieldRequired($this->data['type'], 'link_trans') ? 'required' : 'nullable').'|string|max:255',
            'description_ar'        => (CrudModel::isFieldRequired($this->data['type'], 'description') ? 'required' : 'nullable').'|string|max:30000',
            'image_ar'              => (CrudModel::isFieldRequired($this->data['type'], 'image') && empty($this->data['model']->translateOrFirst()->image) ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080',
            'seo_description_ar'    => (CrudModel::isFieldRequired($this->data['type'], 'seo_description') ? 'required' : 'nullable').'|string|max:30000',
            'about_ar'              => (CrudModel::isFieldRequired($this->data['type'], 'about') ? 'required' : 'nullable').'|string|max:30000',
        ];
        if(CrudModel::getTypeCustomFields($this->data['type'],'custom_fields')){
            foreach(CrudModel::getTypeCustomFields($this->data['type'],'custom_fields') as $key => $custom_field){
                $rules[$key] = $custom_field['required'] == true ? 'required' : 'nullable'.'|string|max:191';
            }
        }
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!empty($request->input('title_'.$locale))){
                    $rules['title_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'title') ? 'required' : 'nullable').'|string|max:191';
                    $rules['brief_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'brief') ? 'required' : 'nullable').'|string|max:500';
                    $rules['link_trans_'.$locale]       = (CrudModel::isFieldRequired($this->data['type'], 'link_trans') ? 'required' : 'nullable').'|string|max:255';
                    $rules['description_'.$locale]      = (CrudModel::isFieldRequired($this->data['type'], 'description') ? 'required' : 'nullable').'|string|max:30000';
                    $rules['image_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'image') ? 'nullable' : 'nullable').'|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
                    $rules['seo_description_'.$locale]  = (CrudModel::isFieldRequired($this->data['type'], 'seo_description') ? 'required' : 'nullable').'|string|max:30000';
                    $rules['about_'.$locale]            = (CrudModel::isFieldRequired($this->data['type'], 'about') ? 'required' : 'nullable').'|string|max:30000';
                    $rules['keywords_'.$locale]         = (CrudModel::isFieldRequired($this->data['type'], 'keywords') ? 'required' : 'nullable').'|string|max:30000';
                    $rules['tags_'.$locale]             = 'nullable|array';
                    $rules['tags_'.$locale.'.*']        = 'nullable|digits_between:1,9|numeric|max:9999999999|min:1';
                }
            }
        }
        // foreach($this->data['model']->translations as $trans){
        //     if($trans->locale != 'ar'){
        //         if( is_null($request->input('title_'.$trans->locale)) ){
        //             $rules['title_'.$trans->locale] = 'required|string|max:191';
        //         }
        //     }
        // }
        if($request->external_attachments){
            $rules['external_attachments.*.att_type']           = 'required|string|max:191';
            $rules['external_attachments.*.att_name']           = 'required|string|max:191';
            $rules['external_attachments.*.att_description']    = 'nullable|string|max:191';
            $rules['external_attachments.*.att_link']           = 'required|string|max:191';
        }
        if($request->external_attachments_new){
            $rules['external_attachments_new.*.type']           = 'required|string|max:191';
            $rules['external_attachments_new.*.name']           = 'required|string|max:191';
            $rules['external_attachments_new.*.description']    = 'nullable|string|max:191';
            $rules['external_attachments_new.*.link']           = 'required|string|max:191';
        }
        // $checkLanguages = 0;
        // foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
        //     if(is_null($request->input('title_'.$locale))){
        //         $checkLanguages++;
        //     }
        // }
        // if(count(LaravelLocalization::getSupportedLocales()) == $checkLanguages){
        //     $rules['title_ar']        = 'required|string|max:191';
        // }
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
                $this->data['model']->slug              = $request->slug;
                $this->data['model']->sort_order        = $request->sort_order;
                $this->data['model']->views             = $request->views;
                $this->data['model']->link              = $request->link;
                $this->data['model']->currency_symbol   = $request->currency_symbol;
                $this->data['model']->currency_value     = $request->currency_value;
                if($this->data['model']->currency_code != 'TRY'){
                    $this->data['model']->currency_code     = $request->currency_code;
                }
                $imageIconPath = null;
                if($request->hasFile('currency_icon')){
                    $imageIconPath                      = $request->file('currency_icon')->store($this->data['type']);
                    $this->data['model']->currency_icon = $imageIconPath;
                }
                $tags = collect();
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('title_'.$locale))){
                        $this->data['model']->{'title:'.$locale}            = $request->input('title_'.$locale);
                        $this->data['model']->{'brief:'.$locale}            = $request->input('brief_'.$locale);
                        $this->data['model']->{'link_trans:'.$locale}       = $request->input('link_trans_'.$locale);
                        $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                        $this->data['model']->{'seo_description:'.$locale}  = $request->input('seo_description_'.$locale);
                        $this->data['model']->{'about:'.$locale}            = $request->input('about_'.$locale);
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
                if (!empty($request->categories_ids)) {
                    $this->data['model']->categories()->sync($request->categories_ids);
                }
                if(CrudModel::getTypeCustomFields($this->data['type'],'custom_fields')){
                    $attributesArray = [];
                    foreach(CrudModel::getTypeCustomFields($this->data['type'],'custom_fields') as $key => $custom_field){
                        $attribut = Attributes::where('key',$key)->where('content_id',$this->data['model']->id)->first();
                        if(!empty($attribut)){
                            $attribut->value = $request->input($key);
                            $attribut->save();
                        }else{
                            $attributesArray[] = [
                                'content_id'        => $this->data['model']->id,
                                'type'              => $custom_field['type'],
                                'key'               => $key,
                                'value'             => $request->input($key),
                            ];
                        }
                    }
                    if(!empty($attributesArray)){
                        Attributes::insert($attributesArray);
                    }
                }
                // if (is_array($request->attachments) && !empty($request->attachments)){
                //     Attachment::whereIn('id', $request->attachments)->update([
                //         'type'              => 'LINKED',
                //         'attachable_id'     => $this->data['model']->id,
                //         'attachable_type'   => get_class($this->data['model']),
                //     ]);
                // }
                if (is_array($request->attachments) && !empty($request->attachments)) {
                    $attachments = Attachment::whereIn('id', $request->attachments)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if($request->external_attachments_new){
                    $this->data['externalAttachments'] = [];
                    foreach ($request->external_attachments_new as $key => $externalAttachment) {
                        $this->data['externalAttachments'][] = [
                            'attachable_type'   => CrudModel::class,
                            'attachable_id'     => $this->data['model']->id,
                            'type'              => $externalAttachment['type'],
                            'name'              => $externalAttachment['name'],
                            'description'       => $externalAttachment['description'],
                            'link'              => $externalAttachment['link'],
                        ];
                    }
                    if (!empty($this->data['externalAttachments'])) {
                        ExternalAttachments::insert($this->data['externalAttachments']);
                    }
                }
                if($request->external_attachments){
                    foreach($request->external_attachments as $key=>$item){
                        $att_for_update = ExternalAttachments::find($key);
                        if(!empty($att_for_update)){
                            $att_for_update->type           = $item['att_type'];
                            $att_for_update->name           = $item['att_name'];
                            $att_for_update->description    = $item['att_description'];
                            $att_for_update->link           = $item['att_link'];
                            $att_for_update->save();
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
            }
            if(array_key_exists('imageIconPath', $this->data)) app()->ImageManipulator->deleteImage($imageIconPath);
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
    public function destroy(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->withTrashed()->with('translations')->findOrFail($request->model);
        if(!$this->data['model']->trashed()){
            $this->authorize('delete', [$this->data['model'],$this->data['model']->type]);
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
            // Check if the authenticated user is allowed to proceed farther.
            $this->authorize('forceDelete', [$this->data['model'],$this->data['model']->type]);
            try {
                DB::transaction(function() use ($request) {
                    foreach($this->data['model']->translations as $trans){
                        if($trans->image) app()->ImageManipulator->deleteImage($trans->image);
                    }
                    if(CrudModel::getTypeCustomFields($this->data['model']->type,'custom_fields')){
                        $this->data['model']->CustomFields()->delete();
                    }
                    if($this->data['model']->currency_icon) app()->ImageManipulator->deleteImage($this->data['model']->currency_icon);
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
    public function destroyTranslation(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->withTrashed()->with('translations')->findOrFail($request->model);
        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('deleteTranslation', [$this->data['model'],$this->data['model']->type]);
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
    public function restore(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->withTrashed()->findOrFail($request->model);
        $this->authorize('restore', [$this->data['model'],$this->data['model']->type]);
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
    public function massDestroy(Request $request)
    {
        $this->data['models'] = CrudModel::withDisabled()->withTrashed()->whereIn('id', explode(',', $request->ids))->get();
        // The model attribute which will be shown to user to indicate the unsuccessful models.
        $this->data['attribute'] = 'title';
        $this->data['failed'] = collect([]);
        // Loop through the selected models to determine which of whom can be deleted.
        foreach($this->data['models'] as $model){
            if(!$model->trashed()){
                try {
                    // Check if the authenticated user is allowed to proceed farther.
                    $this->authorize('delete', [$model,$model->type]);
                    DB::transaction(function() use ($request, $model) {
                        $model->delete();
                    });

                } catch (\Exception $e) {
                    // Push failed models to failed array to notify the user which models could not be deleted.
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }
            }else{
                try {
                    // Check if the authenticated user is allowed to proceed farther.
                    $this->authorize('forceDelete', [$model,$model->type]);
                    DB::transaction(function() use ($request, $model) {
                        foreach($model->translations as $trans){
                            if($trans->image) app()->ImageManipulator->deleteImage($trans->image);
                        }
                        if(CrudModel::getTypeCustomFields($model->type,'custom_fields')){
                            $model->CustomFields()->delete();
                        }
                        if($model->currency_icon) app()->ImageManipulator->deleteImage($model->currency_icon);
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
    public function massRestore(Request $request)
    {
        $this->data['models'] = CrudModel::withDisabled()->onlyTrashed()->whereIn('id', explode(',', $request->ids))->get();
        // The model attribute which will be shown to user to indicate the unsuccessful models.
        $this->data['attribute'] = 'title';
        $this->data['failed'] = collect([]);
        // Loop through the selected models to determine which of whom can be restored.
        foreach($this->data['models'] as $model){
            try {
                // Check if the authenticated user is allowed to proceed farther.
                $this->authorize('restore', [$model,$model->type]);
                DB::transaction(function() use ($request, $model) {
                    $model->restore();
                });
            } catch (\Exception $e) {
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
    public function disable(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail($request->model);
        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('disable', [$this->data['model'],$this->data['model']->type]);
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->disabled_at = \Carbon\Carbon::now()->toDateTimeString();
                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.disable_error.title'),
                'description' => __('cms::messages.disable_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.disable_success.title'),
            'description' => __('cms::messages.disable_success.description')
        ]);
    }
    public function enable(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->findOrFail($request->model);
        // Check if the authenticated user is allowed to proceed farther.
        $this->authorize('enable', [$this->data['model'],$this->data['model']->type]);
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->disabled_at = null;
                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.enable_error.title'),
                'description' => __('cms::messages.enable_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.enable_success.title'),
            'description' => __('cms::messages.enable_success.description')
        ]);
    }
    public function deleteAttachment(Request $request)
    {
        $this->data['model'] = ExternalAttachments::findOrFail($request->attachment_id);
        // The content edit page's ability, for the attachment's own content and type (S21).
        $content = CrudModel::withDisabled()->findOrFail($this->data['model']->attachable_id);
        $this->authorize('update', [$content, $content->type]);
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->delete();
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.delete_success.title'),
                'description' => __('cms::messages.delete_success.description')
            ]);
        }
        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.delete_success.title'),
            'description' => __('cms::messages.delete_success.description')
        ]);
    }
}
