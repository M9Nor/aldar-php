<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Cms\Http\Controllers\CmsController;
use Modules\Cms\Classes\ResponseHandler;
use Modules\Cms\Entities\Area as CrudModel;
use Modules\Cms\Entities\Attributes;
use Validator;
use Bouncer;
use Auth;
use DB;
use LaravelLocalization;
use App\Rules\Slug;

class AreaController extends CmsController
{
    public $attributeNames;

    public function __construct()
    {
        $this->attributeNames = [
            'city'          => __('cms::areas.city'),
            'tags'          => __('cms::areas.fields.tags.label'),
            'tags.*'        => __('cms::areas.fields.tags.label'),
            'native_name'   => __('cms::areas.fields.slug.label'),
        ];
        $translationAttributeNames = [];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $names = [
                'name_'.$locale                 => __('cms::areas.name_'.$locale.''),
                'image_'.$locale                => __('cms::areas.image_'.$locale.''),
                'about_'.$locale                => __('cms::areas.about_'.$locale.''),
                'short_description_'.$locale    => __('cms::areas.short_description_'.$locale.''),
                'details_'.$locale              => __('cms::areas.details_'.$locale.''),
                'keywords_'.$locale             => __('cms::areas.keywords_'.$locale.''),
            ];
            $translationAttributeNames = array_merge($translationAttributeNames,$names);
        }
        $customFieldAttributeNames = [
            'price_change.rent.last_year'                                   => __('cms::areas.custom_fields.price_change.rent.last_year.label'),
            'price_change.rent.last_3_years'                                => __('cms::areas.custom_fields.price_change.rent.last_3_years.label'),
            'price_change.rent.last_5_years'                                => __('cms::areas.custom_fields.price_change.rent.last_5_years.label'),
            'price_change.sale.last_year'                                   => __('cms::areas.custom_fields.price_change.sale.last_year.label'),
            'price_change.sale.last_3_years'                                => __('cms::areas.custom_fields.price_change.sale.last_3_years.label'),
            'price_change.sale.last_5_years'                                => __('cms::areas.custom_fields.price_change.sale.last_5_years.label'),
            'demographic_data.people_data.growth_rate'                      => __('cms::areas.custom_fields.demographic_data.people_data.growth_rate.label'),
            'demographic_data.people_data.total_population'                 => __('cms::areas.custom_fields.demographic_data.people_data.total_population.label'),
            'demographic_data.people_data.ecomomic_and_social_evaluation'   => __('cms::areas.custom_fields.demographic_data.people_data.ecomomic_and_social_evaluation.label'),
            'demographic_data.education_status.uneducated'                  => __('cms::areas.custom_fields.demographic_data.education_status.uneducated.label'),
            'demographic_data.education_status.primary'                     => __('cms::areas.custom_fields.demographic_data.education_status.primary.label'),
            'demographic_data.education_status.elementary'                  => __('cms::areas.custom_fields.demographic_data.education_status.elementary.label'),
            'demographic_data.education_status.secondary'                   => __('cms::areas.custom_fields.demographic_data.education_status.secondary.label'),
            'demographic_data.education_status.university'                  => __('cms::areas.custom_fields.demographic_data.education_status.university.label'),
            'demographic_data.age_distribution.0_to_14'                     => __('cms::areas.custom_fields.demographic_data.age_distribution.0_to_14.label'),
            'demographic_data.age_distribution.15_to_24'                    => __('cms::areas.custom_fields.demographic_data.age_distribution.15_to_24.label'),
            'demographic_data.age_distribution.25_to_34'                    => __('cms::areas.custom_fields.demographic_data.age_distribution.25_to_34.label'),
            'demographic_data.age_distribution.35_to_44'                    => __('cms::areas.custom_fields.demographic_data.age_distribution.35_to_44.label'),
            'demographic_data.age_distribution.45_to_54'                    => __('cms::areas.custom_fields.demographic_data.age_distribution.45_to_54.label'),
            'demographic_data.age_distribution.55_to_64'                    => __('cms::areas.custom_fields.demographic_data.age_distribution.55_to_64.label'),
            'demographic_data.age_distribution.over_65'                     => __('cms::areas.custom_fields.demographic_data.age_distribution.over_65.label'),
            'demographic_data.marital_condition.single'                     => __('cms::areas.custom_fields.demographic_data.marital_condition.single.label'),
            'demographic_data.marital_condition.married'                    => __('cms::areas.custom_fields.demographic_data.marital_condition.married.label'),
            'demographic_data.marital_condition.divorced'                   => __('cms::areas.custom_fields.demographic_data.marital_condition.divorced.label'),
            'demographic_data.marital_condition.widow'                      => __('cms::areas.custom_fields.demographic_data.marital_condition.widow.label'),
        ];
        $this->attributeNames = array_merge($this->attributeNames,$translationAttributeNames);
        $this->attributeNames = array_merge($this->attributeNames,$customFieldAttributeNames);
        $this->middleware('auth')->except([]);
        parent::__construct();
    }
    public function index(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        return view('cms::admin.areas.index', $this->data);
    }
    public function data(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        $list = CrudModel::select(['cms_areas.*',
            DB::raw('
                (
                    SELECT trans.name
                    FROM cms_area_translations AS trans
                    WHERE trans.area_id = cms_areas.id
                    AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
            ')
        ])->with('translations');
        $withTrashed = request('trashed','hide');
        $list->when($withTrashed == 'show', function($query) {
            $query->onlyTrashed();
        });
        $datatables = DataTables::of($list);
        $datatables
        ->addIndexColumn()
        ->addColumn('translated_name', function($model) {
            return !is_null( $model->new_title ) ? $model->new_title : $model->translations->first()->name;
        })
        // ->addColumn('description', function($model){
        //     return $model->description ?? '---';
        // })
        ->addColumn('actions', function($model){
            $items = [];
            $actions['dropdown'] = [];
            $actions['icons'] = [];
            if(!$model->trashed())
            {
                if(auth()->user()->can('update', $model))
                {
                    $items[] = array_merge($this->actions['edit'], [
                        'url'   => route('AreaController@edit', ['model' => $model->id]),
                        'id'    => 'edit_' . $model->id
                    ]);
                }
                if(auth()->user()->can('delete', $model) )
                {
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('AreaController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
            }
            if($model->trashed())
            {
                if(auth()->user()->can('restore', $model))
                {
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('AreaController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', $model))
                {
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('AreaController@destroy', ['model' => $model->id]),
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
        $rawColumns[] = 'description';
        $rawColumns[] = 'area';
        $rawColumns[] = 'actions';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function create(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        return view('cms::admin.areas.create', $this->data);
    }
    public function store(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        $rules = [
            'city'          => 'required|digits_between:1,9|numeric|max:9999999999|min:1',
            'native_name'   => ['required','string',new Slug,'max:191','min:3','unique:cms_areas'],
            'tags_ar'       => 'nullable|array',
            'tags_ar.*'     => 'required|digits_between:1,9|numeric|max:9999999999|min:1',
       ];
       foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $rules['name_'.$locale]                 = 'required|string|max:191';
            $rules['description_'.$locale]          = 'nullable|string|max:10000';
            $rules['about_'.$locale]                = 'nullable|string|max:10000';
            $rules['short_description_'.$locale]    = 'nullable|string|max:10000';
            $rules['details_'.$locale]              = 'nullable|string|max:10000';
            $rules['keywords_'.$locale]             = 'nullable|string|max:10000';
            $rules['image_'.$locale]                = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
            $rules['tags_'.$locale]                 = 'nullable|array';
            $rules['tags_'.$locale.'.*']            = 'nullable|digits_between:1,9|numeric|max:9999999999|min:1';
        }

        $rules['price_change.rent.last_year']                                   = 'required|numeric|min:-50|max:300';
        $rules['price_change.rent.last_3_years']                                = 'required|numeric|min:-50|max:300';
        $rules['price_change.rent.last_5_years']                                = 'required|numeric|min:-50|max:300';
        $rules['price_change.sale.last_year']                                   = 'required|numeric|min:-50|max:300';
        $rules['price_change.sale.last_3_years']                                = 'required|numeric|min:-50|max:300';
        $rules['price_change.sale.last_5_years']                                = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.people_data.growth_rate']                      = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.people_data.total_population']                 = 'required|numeric|min:0';
        $rules['demographic_data.people_data.ecomomic_and_social_evaluation']   = 'required|string';
        $rules['demographic_data.education_status.uneducated']                  = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.education_status.primary']                     = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.education_status.elementary']                  = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.education_status.secondary']                   = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.education_status.university']                  = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.0_to_14']                     = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.15_to_24']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.25_to_34']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.35_to_44']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.45_to_54']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.55_to_64']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.over_65']                     = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.marital_condition.single']                     = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.marital_condition.married']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.marital_condition.divorced']                   = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.marital_condition.widow']                      = 'required|numeric|min:-50|max:300';

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
                $this->data['model']                        = new CrudModel;
                $this->data['model']->city_id               = $request->city;
                $this->data['model']->native_name           = $request->native_name;
                $tags = collect();
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    $this->data['model']->{'name:'.$locale}             = $request->input('name_'.$locale);
                    $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                    $this->data['model']->{'about:'.$locale}            = $request->input('about_'.$locale);
                    $this->data['model']->{'short_description:'.$locale}= $request->input('short_description_'.$locale);
                    $this->data['model']->{'details:'.$locale}          = $request->input('details_'.$locale);
                    $this->data['model']->{'keywords:'.$locale}         = $request->input('keywords_'.$locale);
                    ${'imagePath'.$locale} = null;
                    if($request->hasFile('image_'.$locale)){
                        ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('areas');
                        $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                    }
                    $tags   = $tags->merge($request->{'tags_'.$locale});
                }
                $this->data['model']->save();
                $this->data['model']->tags()->sync($tags->unique());

                $customFields = [
                    'price_change.rent.last_year'                                   => $request->price_change['rent']['last_year'],
                    'price_change.rent.last_3_years'                                => $request->price_change['rent']['last_3_years'],
                    'price_change.rent.last_5_years'                                => $request->price_change['rent']['last_5_years'],
                    'price_change.sale.last_year'                                   => $request->price_change['sale']['last_year'],
                    'price_change.sale.last_3_years'                                => $request->price_change['sale']['last_3_years'],
                    'price_change.sale.last_5_years'                                => $request->price_change['sale']['last_5_years'],
                    'demographic_data.people_data.growth_rate'                      => $request->demographic_data['people_data']['growth_rate'],
                    'demographic_data.people_data.total_population'                 => $request->demographic_data['people_data']['total_population'],
                    'demographic_data.people_data.ecomomic_and_social_evaluation'   => $request->demographic_data['people_data']['ecomomic_and_social_evaluation'],
                    'demographic_data.education_status.uneducated'                  => $request->demographic_data['education_status']['uneducated'],
                    'demographic_data.education_status.primary'                     => $request->demographic_data['education_status']['primary'],
                    'demographic_data.education_status.elementary'                  => $request->demographic_data['education_status']['elementary'],
                    'demographic_data.education_status.secondary'                   => $request->demographic_data['education_status']['secondary'],
                    'demographic_data.education_status.university'                  => $request->demographic_data['education_status']['university'],
                    'demographic_data.age_distribution.0_to_14'                     => $request->demographic_data['age_distribution']['0_to_14'],
                    'demographic_data.age_distribution.15_to_24'                    => $request->demographic_data['age_distribution']['15_to_24'],
                    'demographic_data.age_distribution.25_to_34'                    => $request->demographic_data['age_distribution']['25_to_34'],
                    'demographic_data.age_distribution.35_to_44'                    => $request->demographic_data['age_distribution']['35_to_44'],
                    'demographic_data.age_distribution.45_to_54'                    => $request->demographic_data['age_distribution']['45_to_54'],
                    'demographic_data.age_distribution.55_to_64'                    => $request->demographic_data['age_distribution']['55_to_64'],
                    'demographic_data.age_distribution.over_65'                     => $request->demographic_data['age_distribution']['over_65'],
                    'demographic_data.marital_condition.single'                     => $request->demographic_data['marital_condition']['single'],
                    'demographic_data.marital_condition.married'                    => $request->demographic_data['marital_condition']['married'],
                    'demographic_data.marital_condition.divorced'                   => $request->demographic_data['marital_condition']['divorced'],
                    'demographic_data.marital_condition.widow'                      => $request->demographic_data['marital_condition']['widow'],
                ];

                $customFieldsToInsert = [];

                foreach($customFields as $key => $customField)
                {
                    $customFieldsToInsert[] = [
                        'type'          => 'text',
                        'content_id'    => $this->data['model']->id,
                        'content_type'  => get_class($this->data['model']),
                        'key'           => $key,
                        'value'         => $customField,
                    ];
                }

                Attributes::insert($customFieldsToInsert);
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
    public function edit(Request $request)
    {
        $this->data['model'] = CrudModel::with('translations','tags.translations', 'customFields')->withTrashed()->findOrFail($request->model);

        $this->authorize('update', $this->data['model']);
        return view('cms::admin.areas.update', $this->data);
    }
    public function update(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail("{$request->model}");
        $this->authorize('update', $this->data['model']);
        $rules = [
            'city'          => 'required|digits_between:1,9|numeric|max:9999999999|min:1',
            'native_name'   => ['required','string',new Slug,'max:191','min:3','unique:cms_areas,native_name,'.$request->model.''],
            'tags_ar'       => 'nullable|array',
            'tags_ar.*'     => 'nullable|digits_between:1,9|numeric|max:9999999999|min:1',
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $rules['name_'.$locale]                 = 'required|string|max:191';
            $rules['description_'.$locale]          = 'nullable|string|max:10000';
            $rules['about_'.$locale]                = 'nullable|string|max:10000';
            $rules['short_description_'.$locale]    = 'nullable|string|max:10000';
            $rules['details_'.$locale]              = 'nullable|string|max:10000';
            $rules['keywords_'.$locale]             = 'nullable|string|max:10000';
            $rules['image_'.$locale]                = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
            $rules['tags_'.$locale]                 = 'nullable|array';
            $rules['tags_'.$locale.'.*']            = 'nullable|digits_between:1,9|numeric|max:9999999999|min:1';
        }

        $rules['price_change.rent.last_year']                                   = 'required|numeric|min:-50|max:300';
        $rules['price_change.rent.last_3_years']                                = 'required|numeric|min:-50|max:300';
        $rules['price_change.rent.last_5_years']                                = 'required|numeric|min:-50|max:300';
        $rules['price_change.sale.last_year']                                   = 'required|numeric|min:-50|max:300';
        $rules['price_change.sale.last_3_years']                                = 'required|numeric|min:-50|max:300';
        $rules['price_change.sale.last_5_years']                                = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.people_data.growth_rate']                      = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.people_data.total_population']                 = 'required|numeric|min:0';
        $rules['demographic_data.people_data.ecomomic_and_social_evaluation']   = 'required|string';
        $rules['demographic_data.education_status.uneducated']                  = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.education_status.primary']                     = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.education_status.elementary']                  = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.education_status.secondary']                   = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.education_status.university']                  = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.0_to_14']                     = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.15_to_24']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.25_to_34']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.35_to_44']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.45_to_54']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.55_to_64']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.age_distribution.over_65']                     = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.marital_condition.single']                     = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.marital_condition.married']                    = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.marital_condition.divorced']                   = 'required|numeric|min:-50|max:300';
        $rules['demographic_data.marital_condition.widow']                      = 'required|numeric|min:-50|max:300';

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
                $this->data['model']->city_id           = $request->city;
                $this->data['model']->native_name       = $request->native_name;
                $tags = collect();
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    $this->data['model']->{'name:'.$locale}             = $request->input('name_'.$locale);
                    $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                    $this->data['model']->{'about:'.$locale}            = $request->input('about_'.$locale);
                    $this->data['model']->{'short_description:'.$locale}= $request->input('short_description_'.$locale);
                    $this->data['model']->{'details:'.$locale}          = $request->input('details_'.$locale);
                    $this->data['model']->{'keywords:'.$locale}         = $request->input('keywords_'.$locale);
                    ${'imagePath'.$locale} = null;
                    if($request->hasFile('image_'.$locale)){
                        ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('areas');
                        $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                    }
                    $tags = $tags->merge($request->{'tags_'.$locale});
                }
                $this->data['model']->save();
                $this->data['model']->tags()->sync($tags->unique());
                // if (is_array($request->tags) && !empty($request->tags)) {
                //     $this->data['model']->tags()->sync($request->tags);
                // }

                $customFields = [
                    'price_change.rent.last_year'                                   => $request->price_change['rent']['last_year'],
                    'price_change.rent.last_3_years'                                => $request->price_change['rent']['last_3_years'],
                    'price_change.rent.last_5_years'                                => $request->price_change['rent']['last_5_years'],
                    'price_change.sale.last_year'                                   => $request->price_change['sale']['last_year'],
                    'price_change.sale.last_3_years'                                => $request->price_change['sale']['last_3_years'],
                    'price_change.sale.last_5_years'                                => $request->price_change['sale']['last_5_years'],
                    'demographic_data.people_data.growth_rate'                      => $request->demographic_data['people_data']['growth_rate'],
                    'demographic_data.people_data.total_population'                 => $request->demographic_data['people_data']['total_population'],
                    'demographic_data.people_data.ecomomic_and_social_evaluation'   => $request->demographic_data['people_data']['ecomomic_and_social_evaluation'],
                    'demographic_data.education_status.uneducated'                  => $request->demographic_data['education_status']['uneducated'],
                    'demographic_data.education_status.primary'                     => $request->demographic_data['education_status']['primary'],
                    'demographic_data.education_status.elementary'                  => $request->demographic_data['education_status']['elementary'],
                    'demographic_data.education_status.secondary'                   => $request->demographic_data['education_status']['secondary'],
                    'demographic_data.education_status.university'                  => $request->demographic_data['education_status']['university'],
                    'demographic_data.age_distribution.0_to_14'                     => $request->demographic_data['age_distribution']['0_to_14'],
                    'demographic_data.age_distribution.15_to_24'                    => $request->demographic_data['age_distribution']['15_to_24'],
                    'demographic_data.age_distribution.25_to_34'                    => $request->demographic_data['age_distribution']['25_to_34'],
                    'demographic_data.age_distribution.35_to_44'                    => $request->demographic_data['age_distribution']['35_to_44'],
                    'demographic_data.age_distribution.45_to_54'                    => $request->demographic_data['age_distribution']['45_to_54'],
                    'demographic_data.age_distribution.55_to_64'                    => $request->demographic_data['age_distribution']['55_to_64'],
                    'demographic_data.age_distribution.over_65'                     => $request->demographic_data['age_distribution']['over_65'],
                    'demographic_data.marital_condition.single'                     => $request->demographic_data['marital_condition']['single'],
                    'demographic_data.marital_condition.married'                    => $request->demographic_data['marital_condition']['married'],
                    'demographic_data.marital_condition.divorced'                   => $request->demographic_data['marital_condition']['divorced'],
                    'demographic_data.marital_condition.widow'                      => $request->demographic_data['marital_condition']['widow'],
                ];

                foreach($customFields as $key => $customField)
                {
                    Attributes::updateOrInsert([
                        'type'          => 'text',
                        'content_id'    => $this->data['model']->id,
                        'content_type'  => get_class($this->data['model']),
                        'key'           => $key,
                    ], [
                        'value'         => $customField,
                    ]);
                }

            });
        } catch (\Exception $e) {
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
                'model_id'      => $this->data['model']->id
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
        $this->data['attribute'] = 'full_name';
        $this->data['failed'] = collect([]);
        foreach($this->data['models'] as $model)
        {
            if(!$model->trashed())
            {
                try {
                    $this->authorize('delete', $model);
                    DB::transaction(function() use ($request, $model) {
                        $model->delete();
                    });

                } catch (\Exception $e) {
                    $this->data['failed']->push($model->{$this->data['attribute']});
                }
            }
            else
            {
                try {
                    $this->authorize('forceDelete', $model);
                    DB::transaction(function() use ($request, $model) {
                        $model->forceDelete();
                        if($model->image) app()->ImageManipulator->deleteImage($model->image);
                    });

                } catch (\Exception $e) {
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
        $this->data['attribute'] = 'full_name';
        $this->data['failed'] = collect([]);
        foreach($this->data['models'] as $model)
        {
            try {
                $this->authorize('restore', $model);
                DB::transaction(function() use ($request, $model) {
                    $model->restore();
                });

            } catch (\Exception $e) {
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
}
