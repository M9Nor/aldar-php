<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Cms\Http\Controllers\CmsController;
use Modules\Cms\Classes\ResponseHandler;
use Modules\Cms\Entities\City as CrudModel;
use Modules\Cms\Entities\Attributes;
use Validator;
use Bouncer;
use Auth;
use DB;
use LaravelLocalization;
use App\Rules\Slug;


class CityController extends CmsController
{
    public $attributeNames;

    public function __construct()
    {
        $this->attributeNames = [
            'country'           => __('cms::areas.country'),
            'sort_order'        => __('cms::areas.fields.sort_order.label'),
            'link'              => __('cms::areas.fields.link.label'),
            'native_name'       => __('cms::areas.fields.slug.label'),
        ];
        $translationAttributeNames = [];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $names = [
                'name_'.$locale             => __('cms::areas.name_'.$locale.''),
                'description_'.$locale      => __('cms::areas.description_'.$locale.''),
                'image_'.$locale            => __('cms::areas.image_'.$locale.''),
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
        ];
        $this->attributeNames = array_merge($this->attributeNames,$translationAttributeNames);
        $this->attributeNames = array_merge($this->attributeNames,$customFieldAttributeNames);
        $this->middleware('auth')->except([]);
        parent::__construct();
    }
    public function index(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        return view('cms::admin.cities.index', $this->data);
    }
    public function data(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        $list = CrudModel::select(['cms_cities.*',
            DB::raw('
                (
                    SELECT trans.name
                    FROM cms_city_translations AS trans
                    WHERE trans.city_id = cms_cities.id
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
                        'url'   => route('CityController@edit', ['model' => $model->id]),
                        'id'    => 'edit_' . $model->id
                    ]);
                }
                if(auth()->user()->can('delete', $model) )
                {
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('CityController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
            }
            if($model->trashed())
            {
                if(auth()->user()->can('restore', $model))
                {
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('CityController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', $model))
                {
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('CityController@destroy', ['model' => $model->id]),
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
        return view('cms::admin.cities.create', $this->data);
    }
    public function store(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        $rules = [
            'country'               => 'required|digits_between:1,9|numeric|max:9999999999|min:1',
            'sort_order'            => 'nullable|digits_between:1,9|numeric|max:9999999999|min:1',
            'link'                  => 'nullable|string|url|max:500',
            'native_name'           => ['required','string',new Slug,'max:191','min:3','unique:cms_cities'],
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                $rules['name_'.$locale]             = 'required|string|max:191';
                $rules['description_'.$locale]      = 'nullable|string|max:10000';
                $rules['image_'.$locale]            = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
        }
        $rules['price_change.rent.last_year']                                   = 'required|numeric|max:1000';
        $rules['price_change.rent.last_3_years']                                = 'required|numeric|max:1000';
        $rules['price_change.rent.last_5_years']                                = 'required|numeric|max:1000';
        $rules['price_change.sale.last_year']                                   = 'required|numeric|max:1000';
        $rules['price_change.sale.last_3_years']                                = 'required|numeric|max:1000';
        $rules['price_change.sale.last_5_years']                                = 'required|numeric|max:1000';
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
                $this->data['model']->country_id            = $request->country;
                $this->data['model']->sort_order            = $request->sort_order;
                $this->data['model']->link                  = $request->link;
                $this->data['model']->native_name           = $request->native_name;
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    $this->data['model']->{'name:'.$locale}             = $request->input('name_'.$locale);
                    $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                    ${'imagePath'.$locale} = null;
                    if($request->hasFile('image_'.$locale)){
                        ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('areas');
                        $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                    }
                }
                $this->data['model']->save();
                if (is_array($request->tags) && !empty($request->tags)) {
                    $this->data['model']->tags()->sync($request->tags);
                }

                $customFields = [
                    'price_change.rent.last_year'                                   => $request->price_change['rent']['last_year'],
                    'price_change.rent.last_3_years'                                => $request->price_change['rent']['last_3_years'],
                    'price_change.rent.last_5_years'                                => $request->price_change['rent']['last_5_years'],
                    'price_change.sale.last_year'                                   => $request->price_change['sale']['last_year'],
                    'price_change.sale.last_3_years'                                => $request->price_change['sale']['last_3_years'],
                    'price_change.sale.last_5_years'                                => $request->price_change['sale']['last_5_years'],
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
    public function edit(Request $request)
    {
        $this->data['model'] = CrudModel::withTrashed()->with('customFields')->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        return view('cms::admin.cities.update', $this->data);
    }
    public function update(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail("{$request->model}");
        $this->authorize('update', $this->data['model']);
        $rules = [
            'country'           => 'required|digits_between:1,9|numeric|max:9999999999|min:1',
            'sort_order'        => 'nullable|digits_between:1,9|numeric|max:9999999999|min:1',
            'link'              => 'nullable|string|url|max:500',
            'native_name'       => ['required','string',new Slug,'max:191','min:3','unique:cms_cities,native_name,'.$request->model.''],
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $rules['name_'.$locale]             = 'required|string|max:191';
            $rules['description_'.$locale]      = 'nullable|string|max:10000';
            $rules['image_'.$locale]            = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
        }
        $rules['price_change.rent.last_year']                                   = 'required|numeric|max:1000';
        $rules['price_change.rent.last_3_years']                                = 'required|numeric|max:1000';
        $rules['price_change.rent.last_5_years']                                = 'required|numeric|max:1000';
        $rules['price_change.sale.last_year']                                   = 'required|numeric|max:1000';
        $rules['price_change.sale.last_3_years']                                = 'required|numeric|max:1000';
        $rules['price_change.sale.last_5_years']                                = 'required|numeric|max:1000';
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
                $this->data['model']->country_id        = $request->country;
                $this->data['model']->sort_order        = $request->sort_order;
                $this->data['model']->link              = $request->link;
                $this->data['model']->native_name       = $request->native_name;
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    $this->data['model']->{'name:'.$locale}         = $request->input('name_'.$locale);
                    $this->data['model']->{'description:'.$locale}  = $request->input('description_'.$locale);
                    ${'imagePath'.$locale} = null;
                    if($request->hasFile('image_'.$locale)){
                        ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('areas');
                        $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                    }
                }
                $this->data['model']->save();

                $customFields = [
                    'price_change.rent.last_year'                                   => $request->price_change['rent']['last_year'],
                    'price_change.rent.last_3_years'                                => $request->price_change['rent']['last_3_years'],
                    'price_change.rent.last_5_years'                                => $request->price_change['rent']['last_5_years'],
                    'price_change.sale.last_year'                                   => $request->price_change['sale']['last_year'],
                    'price_change.sale.last_3_years'                                => $request->price_change['sale']['last_3_years'],
                    'price_change.sale.last_5_years'                                => $request->price_change['sale']['last_5_years'],
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
