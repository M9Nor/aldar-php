<?php

namespace Modules\Backend\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Cms\Http\Controllers\CmsController;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\Content;
use Modules\Cms\Classes\ResponseHandler;
use Modules\Backend\Entities\Project  as CrudModel;
use Modules\Backend\Entities\Price;
use Modules\Backend\Entities\ContactUs ;
use Modules\Backend\Entities\PayingMethod;
use Modules\Cms\Entities\Attachment;
use Modules\Cms\Entities\Country;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\Area;
use Modules\Cms\Entities\Tag;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Validator;
use Bouncer;
use Auth;
use DB;
use LaravelLocalization;
use App\Rules\Slug;
use Modules\Backend\Entities\PropertyForm;
use Illuminate\Support\Facades\Storage;

class ProjectController extends CmsController
{
    public $attributeNames;
    public function __construct()
    {
        $this->attributeNames = [
            'code'                          => __('backend::projects.fields.code.label'),
            'slug'                          => __('backend::projects.fields.slug.label'),
            'sort_order'                    => __('backend::projects.fields.sort_order.label'),
            'link'                          => __('backend::projects.fields.link.label'),
            // 'bulding_date'                  => __('backend::projects.fields.bulding_date.label'),
            'agent'                         => __('backend::projects.fields.agent.label'),
            'categories_id'                 => __('backend::projects.fields.category.label'),
            'project_type'                  => __('backend::projects.fields.type.label'),
            'offers.*'                      => __('backend::projects.fields.offers.label'),
            'offers'                        => __('backend::projects.fields.offers.label'),
            'status.*'                      => __('backend::projects.fields.status.label'),
            'status'                        => __('backend::projects.fields.status.label'),
            'features.*'                    => __('backend::projects.fields.features.label'),
            'features'                      => __('backend::projects.fields.features.label'),
            'country'                       => __('backend::projects.fields.country.label'),
            'city'                          => __('backend::projects.fields.city.label'),
            'pro_area'                      => __('backend::projects.fields.area.label'),
            'areas.*'                       => __('backend::projects.fields.areas.label'),
            'areas'                         => __('backend::projects.fields.areas.label'),
            'sea'                           => __('backend::projects.fields.sea.label'),
            'pro_city_distance'             => __('backend::projects.fields.city_distance.label'),
            'airport'                       => __('backend::projects.fields.airport.label'),
            'school'                        => __('backend::projects.fields.school.label'),
            'university'                    => __('backend::projects.fields.university.label'),
            'hospital'                      => __('backend::projects.fields.hospital.label'),
            'mosque'                        => __('backend::projects.fields.mosque.label'),
            'facilities.*'                  => __('backend::projects.sections.service.title'),
            'facilities'                    => __('backend::projects.sections.service.title'),
            'tags.*'                        => __('backend::projects.fields.tags.label'),
            'tags'                          => __('backend::projects.fields.tags.label'),
            'turkish_nationality'           => __('backend::projects.fields.turkish_nationality.label'),
            'keywords'                      => __('backend::projects.fields.keywords.label'),
            'top_features'                  => __('backend::projects.fields.top_features.label'),
            'top_offers'                    => __('backend::projects.fields.top_offers.label'),
            'top_type'                      => __('backend::projects.fields.top_type.label'),
            'disabled_at'                   => __('backend::projects.fields.disabled_at.label'),
            'is_featured'                   => __('backend::projects.fields.is_featured.label'),
            'is_statistics'                 => __('backend::projects.fields.is_statistics.label'),

            // repeaters
            'paying.*.pay_id'               => __('backend::projects.fields.pay.label'),
            'paying.*.first_pay'            => __('backend::projects.fields.first_pay.label'),
            'paying.*.number_pays'          => __('backend::projects.fields.number_pays.label'),
            'paying.*.note_pays'            => __('backend::projects.fields.note_pays.label'),
            'new_paying.*.pay_id'           => __('backend::projects.fields.pay.label'),
            'new_paying.*.first_pay'        => __('backend::projects.fields.first_pay.label'),
            'new_paying.*.number_pays'      => __('backend::projects.fields.number_pays.label'),
            'new_paying.*.note_pays'        => __('backend::projects.fields.note_pays.label'),
            'prices.*.balance'              => __('backend::projects.fields.balance.label'),
            'prices.*.projecttypeprice'     => __('backend::projects.fields.projecttypeprice.label'),
            'prices.*.is_sold'              => __('backend::projects.is_sold'),
            'prices.*.room'                 => __('backend::projects.fields.room.label'),
            'prices.*.salon'                => __('backend::projects.fields.salon.label'),
            // 'prices.*.bath'                 => __('backend::projects.fields.bath.label'),
            'prices.*.lowest_area'          => __('backend::projects.fields.lowest_area.label'),
            'prices.*.highest_area'         => __('backend::projects.fields.highest_area.label'),
            'prices.*.lowest_price'         => __('backend::projects.fields.lowest_price.label'),
            'prices.*.highest_price'        => __('backend::projects.fields.highest_price.label'),
            'prices.*.discount'             => __('backend::projects.fields.discount.label'),
            
            'new_prices.*.balance'          => __('backend::projects.fields.balance.label'),
            'new_prices.*.projecttypeprice' => __('backend::projects.fields.projecttypeprice.label'),
            'new_prices.*.is_sold'          => __('backend::projects.is_sold'),
            'new_prices.*.room'             => __('backend::projects.fields.room.label'),
            'new_prices.*.salon'            => __('backend::projects.fields.salon.label'),
            // 'new_prices.*.bath'             => __('backend::projects.fields.bath.label'),
            'new_prices.*.lowest_area'      => __('backend::projects.fields.lowest_area.label'),
            'new_prices.*.highest_area'     => __('backend::projects.fields.highest_area.label'),
            'new_prices.*.lowest_price'     => __('backend::projects.fields.lowest_price.label'),
            'new_prices.*.highest_price'    => __('backend::projects.fields.highest_price.label'),
            'new_prices.*.discount'         => __('backend::projects.fields.discount.label'),
        ];
        $translationAttributeNames = [];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $names = [
                'image_'.$locale                => __('backend::projects.image_'.$locale.''),
                'title_'.$locale                => __('backend::projects.title_'.$locale.''),
                'landing_page_title_'.$locale   => __('backend::projects.landing_page_title_'.$locale.''),
                'landing_page_desc_'.$locale    => __('backend::projects.landing_page_desc_'.$locale.''),
                'description_'.$locale      => __('backend::projects.description_'.$locale.''),
                'details_'.$locale          => __('backend::projects.details_'.$locale.''),
                'about_'.$locale            => __('backend::projects.about_'.$locale.''),
                'brief_'.$locale            => __('backend::projects.brief_'.$locale.''),
                'second_title_'.$locale     => __('backend::projects.second_title_'.$locale.''),
                'tab_title_'.$locale        => __('backend::projects.tab_title_'.$locale.''),
                'seo_description_'.$locale  => __('backend::projects.seo_description_'.$locale.''),
                'trans_keywords_'.$locale   => __('backend::projects.trans_keywords_'.$locale.''),
                'delivery_date_'.$locale    => __('backend::projects.delivery_date_'.$locale.''),
                'video_'.$locale            => __('backend::projects.video_'.$locale.''),
                'video2_'.$locale           => __('backend::projects.video2_'.$locale.''),
                'project_image1_'.$locale   => __('backend::projects.project_image1_'.$locale.''),
                'project_image2_'.$locale   => __('backend::projects.project_image2_'.$locale.''),
            ];
            $translationAttributeNames      = array_merge($translationAttributeNames,$names);
        }
        $this->attributeNames = array_merge($this->attributeNames,$translationAttributeNames);
        $this->middleware('auth')->except([]);
        parent::__construct();
    }
    public function updatePrices($id){
        $projects               = CrudModel::where('id',$id)->with('prices')->orderBy('id','DESC')->get();
        $desiredCurrencyCode    = 'TRY';
        $currencies             = Content::where('type','currencies')->get();
        $desiredCurrency        = $currencies->where('currency_code', $desiredCurrencyCode)->first();
        $currencyValue          = (float) $desiredCurrency->currency_value;

        foreach($projects as $project){
            if(!empty($project->prices)){
                foreach($project->prices as $p){
                    $highest_price      = (float) $p->getRawOriginal('highest_price');
                    $lowest_price       = (float) $p->getRawOriginal('lowest_price');
                    $currentCurrency    = Content::find($p->balance_id);
                    // dump($currentCurrency);
                    $highest_price  = $highest_price / (float) $currentCurrency->currency_value;
                    $lowest_price   = $lowest_price / (float) $currentCurrency->currency_value;
                    // dd($highest_price,$currentCurrency->currency_value);
                    $p->full_highest_price  = round($highest_price,2);
                    $p->full_lowest_price   = round($lowest_price,2);
                    $p->save();
                }
            }
        }

        // dd($projects);
    }
    public function index(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        $this->data['statuses']                 = CrudModel::collectStatuses();
        $this->data['countries']                = Country::with('translations')->whereHas('translations')->get();
        $this->data['cities']                   = City::with('translations')->whereHas('translations')->get();
        $this->data['areas']                    = Area::with('translations')->whereHas('translations')->get();

        $this->data['projectCategories']        = Category::with('translations')->whereIn('type',['contracts','property_classifications','property_status','property_features','facilities','payments'])->get();
        $this->data['contracts']                = clone $this->data['projectCategories']->where('type','contracts');
        $this->data['property_classifications'] = clone $this->data['projectCategories']->where('type','property_classifications')->whereNull('parent_id');
        $this->data['property_type']            = clone $this->data['projectCategories']->where('type','property_classifications')->whereNotNull('parent_id');
        $this->data['property_features']        = clone $this->data['projectCategories']->where('type','property_features');
        $this->data['payments']                 = clone $this->data['projectCategories']->where('type','payments')->whereNull('parent_id');
        $this->data['number_pays']              = clone $this->data['projectCategories']->where('type','payments')->whereNotNull('parent_id');

        // dd($this->data['property_type']);
        $this->data['projectContents']          = Content::where('type',['agents','currencies'])->get();
        $this->data['agents']                   = clone $this->data['projectContents']->where('type','agents');
        return view('backend::admin.projects.index', $this->data);
    }
    public function data(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        $list = CrudModel::query();
        $list = CrudModel::select(['be_projects.*',
            DB::raw('
                (
                    SELECT trans.title
                    FROM be_projects_translations AS trans
                    WHERE trans.project_id = be_projects.id
                    AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
            ')
        ])->with('translations','allCategories.translations')->whereHas('allCategories', function ($query) {
            $query->where('type', 'property_classifications');
        })->withDisabled();
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
                })->when(!empty($filter['country']), function($query) use ($filter) {
                    $query->where('country_id',$filter['country']);
                })->when(!empty($filter['city']), function($query) use ($filter) {
                    $query->where('city_id',$filter['city']);
                })->when(!empty($filter['area']), function($query) use ($filter) {
                    $query->where('area_id',$filter['area']);
                })->when(!empty($filter['status']), function($query) use ($filter) {
                    if($filter['status'] == 'ACTIVE'){
                        $query->whereNull('disabled_at');
                    }else{
                        $query->whereNotNull('disabled_at');
                    }
                })->when(!empty($filter['agent']), function($query) use ($filter) {
                    $query->whereHas('contents',function($q) use ($filter){
                        $q->where('content_id',$filter['agent']);
                    });
                })->when(!empty($filter['contracts']), function($query) use ($filter) {
                    $query->whereHas('categories',function($q) use ($filter){
                        $q->where('id',$filter['contracts']);
                    });
                })->when(!empty($filter['property_classifications']), function($query) use ($filter) {
                    $query->whereHas('categories',function($q) use ($filter){
                        $q->where('id',$filter['property_classifications']);
                    });
                })->when(!empty($filter['property_type']), function($query) use ($filter) {
                    $query->whereHas('categories',function($q) use ($filter){
                        $q->where('id',$filter['property_type']);
                    });
                })
                ->when(!empty($filter['payments']), function($query) use ($filter) {
                    $query->whereHas('payments',function($q) use ($filter){
                        $q->whereHas('payCategory',function($qu) use ($filter){
                            $qu->where('project_type_id',$filter['payments']);
                        });
                    });
                })
                ->when(!empty($filter['number_pays']), function($query) use ($filter) {
                    $query->whereHas('payments',function($q) use ($filter){
                        $q->whereHas('numberCategory',function($qu) use ($filter){
                            $qu->where('number_id',$filter['number_pays']);
                        });
                    });
                })->when(!empty($filter['first_pay']), function($query) use ($filter) {
                    $query->whereHas('payments',function($q) use ($filter){
                        $q->where('first_pay',$filter['first_pay']);
                    });
                })->when(!empty($filter['room']), function($query) use ($filter) {
                    $query->whereHas('prices',function($q) use ($filter){
                        $q->where('room_number',$filter['room']);
                    });
                })->when(!empty($filter['salon']), function($query) use ($filter) {
                    $query->whereHas('prices',function($q) use ($filter){
                        $q->where('salons_number',$filter['salon']);
                    });
                })->when(!empty($filter['lowest_area']), function($query) use ($filter) {
                    $query->whereHas('prices',function($q) use ($filter){
                        $q->where('lowest_area',$filter['lowest_area']);
                    });
                })->when(!empty($filter['highest_area']), function($query) use ($filter) {
                    $query->whereHas('prices',function($q) use ($filter){
                        $q->where('highest_area',$filter['highest_area']);
                    });
                })->when(!empty($filter['lowest_price']), function($query) use ($filter) {
                    $query->whereHas('prices',function($q) use ($filter){
                        $q->where('lowest_price',$filter['lowest_price']);
                    });
                })->when(!empty($filter['highest_price']), function($query) use ($filter) {
                    $query->whereHas('prices',function($q) use ($filter){
                        $q->where('highest_price',$filter['highest_price']);
                    });
                });
            }
        })
        ->addColumn('project', function(CrudModel $model) {
            return json_encode($model);
        })
        ->addColumn('project_status', function($model){
            return [
                'label' => __('backend::projects.statuses.' . $model->status . '.label'),
                'color' => __('backend::projects.statuses.' . $model->status . '.color'),
            ];
        })
        ->addColumn('image', function($model){
            return [
                // 'src' => $model->translate() == NULL ? CrudModel::getImage(new CrudModel, '85x85') : CrudModel::getImage($model->translate(), '85x85')
                'src'   => $model->translateOrFirst(app()->getLocale())->getImage('85x85')
            ];
        })
        ->addColumn('translated_name', function($model) {
            $category = $model->allCategories->where('type','property_classifications')->where('pivot.options', 'top_type')->first();
            if(is_null($category))
            {
                $category = $model->allCategories->where('type','property_classifications')->first();
            }
            $new_title  = !is_null( $model->new_title ) ? $model->new_title : $model->translations->first()->title;
            return '<a style="font-weight: bold; color: #595d6e;" target="_blank" rel="noopener" rel="noopener" href="'.route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'unknown'), 'slug' => $model->slug]).'">'.$new_title.'</a>';
        })->addColumn('languages', function($model){
            $output = '';
            foreach($model->translations->sortBy('locale') as $translation)
            {
                if($translation->locale == 'ar') {
                    $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold"  aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
                } else {
                    $output .= '<div class="dropdown dropdown-inline mx-1"><button type="button" class="btn btn-clean btn-bold" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false">'.strtoupper($translation->locale).'</button>';
                }

                if(auth()->user()->can('deleteTranslation', $model) && $translation->locale != 'ar')
                {
                    $output .= '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">
                        <a class="dropdown-item" href="javascript:;" onclick="deleteTranslationConfirmation(null, function(){ $(`#deleteTranslationForm'.$model->id.'`).submit(); })">
                            '.__('cms::global.actions.delete_translation').'
                        </a>
                        <form id="deleteTranslationForm'.$model->id.'" onsubmit="onFormSubmit(event);" method="POST" action="'.route('ProjectController@destroyTranslation', ['model' => $model->id, 'locale' => $translation->locale]).'" style="display: none;">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                        </form>
                    </div>';
                }
                $output .= '</div>';
            }
            return $output;
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
                        'url'   => route('ProjectController@edit', ['model' => $model->id]),
                        'id'    => 'edit_' . $model->id
                    ]);
                }
                if(auth()->user()->can('delete', $model))
                {
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('ProjectController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
                if(is_null($model->disabled_at))
                {
                    if(auth()->user()->can('disable', $model)){
                        $items[] = array_merge($this->actions['disable'], [
                            'url'   => route('ProjectController@disable', ['model' => $model->id]),
                            'id'    => 'disable_' . $model->id
                        ]);
                    }
                }else{
                    if(auth()->user()->can('enable', $model)){
                        $items[] = array_merge($this->actions['enable'], [
                            'url'   => route('ProjectController@enable', ['model' => $model->id]),
                            'id'    => 'enable_' . $model->id
                        ]);
                    }
                }
                if(is_null($model->is_special) || $model->is_special == 0)
                {
                    if(auth()->user()->can('special', $model)){
                        $items[] = array_merge($this->actions['special'], [
                            'url'   => route('ProjectController@special', ['model' => $model->id]),
                            'id'    => 'special_' . $model->id
                        ]);
                    }
                }else{
                    if(auth()->user()->can('not_special', $model)){
                        $items[] = array_merge($this->actions['not_special'], [
                            'url'   => route('ProjectController@notSpecial', ['model' => $model->id]),
                            'id'    => 'not_special_' . $model->id
                        ]);
                    }
                }
                if(is_null($model->is_sold) || $model->is_sold == 0)
                {
                    if(auth()->user()->can('update', $model)){
                        $items[] = array_merge($this->actions['sold'], [
                            'url'   => route('ProjectController@sold', ['model' => $model->id]),
                            'id'    => 'sold_' . $model->id
                        ]);
                    }
                }else{
                    if(auth()->user()->can('update', $model)){
                        $items[] = array_merge($this->actions['not_sold'], [
                            'url'   => route('ProjectController@notSold', ['model' => $model->id]),
                            'id'    => 'not_sold_' . $model->id
                        ]);
                    }
                }
                if(auth()->user()->can('update', $model)){
                    $items[] = array_merge($this->actions['copy'], [
                        'url'   => route('ProjectController@copy', ['model' => $model->id]),
                        'id'    => 'copy_' . $model->id
                    ]);
                }
            }
            if($model->trashed())
            {
                if(auth()->user()->can('restore', $model))
                {
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('ProjectController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', $model))
                {
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('ProjectController@destroy', ['model' => $model->id]),
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
        $rawColumns[] = 'project';
        $rawColumns[] = 'actions';
        $rawColumns[] = 'languages';
        $rawColumns[] = 'translated_name';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function create(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        
        $this->data['projectCategories']        = Category::with('translations')->whereIn('type',['contracts','property_classifications','property_status','property_features','facilities','payments'])->get();
        $this->data['contracts']                = $this->data['projectCategories']->where('type','contracts');
        // $this->data['property_classifications'] = $this->data['projectCategories']->whereNull('parent_id')->where('type','property_classifications');
        $this->data['property_status']          = $this->data['projectCategories']->where('type','property_status');
        // dd($this->data['property_status']);
        $this->data['property_features']        = $this->data['projectCategories']->where('type','property_features');
        $this->data['facilities']               = $this->data['projectCategories']->where('type','facilities');
        // $this->data['payments']                 = $this->data['projectCategories']->where('type','payments');
        $this->data['projectContents']          = Content::where('type',['agents','currencies'])->get();
        $this->data['agents']                   = $this->data['projectContents'] ->where('type','agents');
        // $this->data['currencies']               = $this->data['projectContents'] ->where('type','currencies');

        $this->data['country']                  = Country::where('id',1)->first();
        $this->data['city']                     = City::where('id',10)->first();
        return view('backend::admin.projects.create', $this->data);
    }
    public function store(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        $rules = [
            'image_ar'              => 'required|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'title_ar'              => 'required|string|max:191',
            'landing_page_title_ar' => 'nullable|string|max:255',
            'landing_page_desc_ar'  => 'nullable|string|max:5000',
            'description_ar'        => 'nullable|string|max:20000',
            'details_ar'            => 'nullable|string|max:20000',
            'about_ar'              => 'nullable|string|max:20000',
            'brief_ar'              => 'nullable|string|max:1000',
            'second_title_ar'       => 'nullable|string|max:255',
            'tab_title_ar'          => 'nullable|string|max:255',
            'seo_description_ar'    => 'nullable|string|max:20000',
            'trans_keywords_ar'     => 'nullable|string|max:20000',
            'delivery_date_ar'      => 'nullable|string|max:255',
            'video_ar'              => 'nullable|string|max:255',
            'video2_ar'             => 'nullable|string|max:255',
            'project_image1_ar'     => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'project_image2_ar'     => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'tags_ar'               => 'nullable|array',
            'tags_ar.*'             => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'turkish_nationality'   => 'in:0,1',
            'code'                  => 'required|string|max:191|unique:be_projects',
            'slug'                  => ['required','string',new Slug,'max:191','min:3','unique:be_projects'],
            'sort_order'            => 'nullable|digits_between:1,9|numeric|max:999999999|min:1',
            'link'                  => 'nullable|string|max:255',

            // 'bulding_date'          => 'nullable|string|max:191',
            'agent'                 => 'nullable|digits_between:1,9|numeric|max:999999999|min:1',
            'categories_id'         => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'project_type'          => 'required|array',
            'project_type.*'        => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'top_type'              => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'offers'                => 'required|array',
            'offers.*'              => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'status'                => 'required|array',
            'status.*'              => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'features'              => 'required|array',
            'features.*'            => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'top_features'          => 'required|array',
            'top_features.*'        => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'top_offers'            => 'required|array',
            'top_offers.*'          => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'country'               => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'city'                  => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'pro_area'              => 'nullable|digits_between:1,9|numeric|max:999999999|min:1',
            'areas'                 => 'nullable|array',
            'areas.*'               => 'nullable|digits_between:1,9|numeric|max:999999999|min:1',
            'sea'                   => 'nullable|string|max:191',
            'pro_city_distance'     => 'nullable|string|max:191',
            'airport'               => 'nullable|string|max:191',
            'school'                => 'nullable|string|max:191',
            'university'            => 'nullable|string|max:191',
            'hospital'              => 'nullable|string|max:191',
            'mosque'                => 'nullable|string|max:191',
            'facilities'            => 'nullable|array',
            'facilities.*'          => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'keywords'              => 'nullable|string|max:10000',
            'disabled_at'           => 'in:yes,no',
            'is_featured'           => 'in:yes,no',
            'is_statistics'         => 'in:yes,no',

        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!is_null($request->input('title_'.$locale))){
                    $rules['image_'.$locale]            = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                    $rules['project_image1_'.$locale]   = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                    $rules['project_image2_'.$locale]   = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                    $rules['title_'.$locale]            = 'required|string|max:191';
                    $rules['landing_page_title_'.$locale]   = 'nullable|string|max:255';
                    $rules['landing_page_desc_'.$locale]    = 'nullable|string|max:5000';
                    $rules['description_'.$locale]      = 'required|string|max:20000';
                    $rules['details_'.$locale]          = 'required|string|max:20000';
                    $rules['about_'.$locale]            = 'required|string|max:20000';
                    $rules['brief_'.$locale]            = 'required|string|max:1000';
                    $rules['second_title_'.$locale]     = 'nullable|string|max:255';
                    $rules['tab_title_'.$locale]        = 'nullable|string|max:255';
                    $rules['seo_description_'.$locale]  = 'nullable|string|max:20000';
                    $rules['trans_keywords_'.$locale]   = 'nullable|string|max:20000';
                    $rules['delivery_date_'.$locale]    = 'nullable|string|max:255';
                    $rules['video_'.$locale]            = 'nullable|string|max:255';
                    $rules['video2_'.$locale]           = 'nullable|string|max:255';
                    $rules['tags_'.$locale]             = 'nullable|array';
                    $rules['tags_'.$locale.'.*']        = 'nullable|digits_between:1,9|numeric|max:999999999|min:1';
                }
            }
        }
        if(!empty($request->paying)){
            $rules['paying.*.pay_id']           = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['paying.*.first_pay']        = 'required|string|max:191';
            $rules['paying.*.number_pays']      = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['paying.*.note_pays_ar']     = 'nullable|string|max:5000';
            $rules['paying.*.note_pays_en']     = 'nullable|string|max:5000';
            $rules['paying.*.note_pays_fa']     = 'nullable|string|max:5000';
            // $rules['paying.*.note_pays_tr']     = 'nullable|string|max:5000';

        }
        if(!empty($request->prices)){
            $rules['prices.*.balance']          = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['prices.*.projecttypeprice'] = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['prices.*.is_sold']          = 'nullable|string|max:10|min:1';
            $rules['prices.*.room']             = 'nullable|numeric|max:999|min:0';
            $rules['prices.*.salon']            = 'nullable|numeric|max:999999999|min:0';
            // $rules['prices.*.bath']             = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.lowest_area']      = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.highest_area']     = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.lowest_price']     = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.highest_price']    = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.discount']         = 'nullable|numeric|max:100|min:0';
            
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
                $this->data['model']->code                  = $request->code;
                $this->data['model']->slug                  = $request->slug;
                $this->data['model']->sort_order            = $request->sort_order;
                if($request->company_and_project == 'yes'){
                    $this->data['model']->link                  = $request->link;
                }
                // $this->data['model']->establishment_date    = $request->bulding_date;
                $this->data['model']->country_id            = $request->country;
                $this->data['model']->city_id               = $request->city;
                $this->data['model']->sea                   = $request->sea;
                $this->data['model']->area_id               = $request->pro_area;
                $this->data['model']->city_distance         = $request->pro_city_distance;
                $this->data['model']->airport               = $request->airport;
                $this->data['model']->school                = $request->school;
                $this->data['model']->university            = $request->university;
                $this->data['model']->hospital              = $request->hospital;
                $this->data['model']->mosque                = $request->mosque;
                $this->data['model']->mall                  = $request->mall;
                $this->data['model']->turkish_nationality   = $request->turkish_nationality;
                $this->data['model']->keywords              = $request->keywords;
                if($request->disabled_at == 'yes'){
                    $this->data['model']->disabled_at       = NULL;
                }else{
                    $this->data['model']->disabled_at       = \Carbon\Carbon::now()->toDateTimeString();
                }
                $this->data['model']->is_featured              = $request->is_featured;
                $this->data['model']->is_statistics            = $request->is_statistics;
                
                $tags = collect();
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('title_'.$locale))){
                        ${'imagePath'.$locale} = null;
                        if($request->hasFile('image_'.$locale)){
                            ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('projects');
                            $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                        }
                        $this->data['model']->{'title:'.$locale}            = $request->input('title_'.$locale);
                        $this->data['model']->{'landing_page_title:'.$locale}   = $request->input('landing_page_title_'.$locale);
                        $this->data['model']->{'landing_page_desc:'.$locale}    = $request->input('landing_page_desc_'.$locale);
                        $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                        $this->data['model']->{'details:'.$locale}          = $request->input('details_'.$locale);
                        $this->data['model']->{'about:'.$locale}            = $request->input('about_'.$locale);
                        $this->data['model']->{'brief:'.$locale}            = $request->input('brief_'.$locale);
                        if($request->company_and_project == 'yes'){
                            $this->data['model']->{'second_title:'.$locale}     = $request->input('second_title_'.$locale);
                        }
                        $this->data['model']->{'tab_title:'.$locale}        = $request->input('tab_title_'.$locale);
                        $this->data['model']->{'seo_description:'.$locale}  = $request->input('seo_description_'.$locale);
                        $this->data['model']->{'trans_keywords:'.$locale}   = $request->input('trans_keywords_'.$locale);
                        $this->data['model']->{'delivery_date:'.$locale}    = $request->input('delivery_date_'.$locale);
                        $this->data['model']->{'video:'.$locale}            = $request->input('video_'.$locale);
                        $this->data['model']->{'video2:'.$locale}           = $request->input('video2_'.$locale);
                        ${'projectImagePath1'.$locale} = null;
                        if($request->hasFile('project_image1_'.$locale)){
                            ${'projectImagePath1'.$locale}                              = $request->file('project_image1_'.$locale)->store('projects');
                            $this->data['model']->{'project_image1:'.$locale}   = ${'projectImagePath1'.$locale};
                        }
                        ${'projectImagePath2'.$locale} = null;
                        if($request->hasFile('project_image2_'.$locale)){
                            ${'projectImagePath2'.$locale}                              = $request->file('project_image2_'.$locale)->store('projects');
                            $this->data['model']->{'project_image2:'.$locale}   = ${'projectImagePath2'.$locale};
                        }
                    }
                    $tags = $tags->merge($request->{'tags_'.$locale});
                }
                $this->data['model']->save();
                // save tags
                $this->data['model']->tags()->sync($tags->unique());
                // save project categories
                $projectCategories     =   [];
                if(!empty($request->offers)){
                    foreach($request->offers as $offer)
                    {
                        $projectCategories[$offer] = ['options' => in_array($offer, $request->top_offers) ? 'top_offer' : null];
                    }
                }
                if(!empty($request->categories_id))
                {
                    $projectCategories[$request->categories_id] = [];
                }
                // if(!empty($request->project_type))
                // {
                //     $projectCategories[$request->project_type] = [];
                // }
                if(!empty($request->project_type)){
                    foreach($request->project_type as $projectType)
                    {
                        $projectCategories[$projectType] = ['options' => $projectType == $request->top_type ? 'top_type' : null];
                    }
                }
                if(!empty($request->status)){
                    foreach($request->status as $stat)
                    {
                        $projectCategories[$stat] = [];
                    }
                }
                if(!empty($request->features)){
                    foreach($request->features as $feature)
                    {
                        $projectCategories[$feature] = ['options' => in_array($feature, $request->top_features) ? 'top_feature' : null];
                    }
                }
                if(!empty($request->facilities)){
                    foreach($request->facilities as $facility)
                    {
                        $projectCategories[$facility] = [];
                    }
                }
                if(!empty($projectCategories)){
                    $this->data['model']->categories()->sync($projectCategories);
                }
                // save projec contents
                $projectContents     =   [];
                if (!empty($request->agent)) {
                    $projectContents[]      = $request->agent;
                }
                if(!empty($projectContents)){
                    $this->data['model']->contents()->sync($projectContents);
                }
                // save project areas
                if (!empty($request->areas)) {
                    $this->data['model']->areas()->sync($request->areas);
                }
                // forms repeates
                if(!empty($request->paying)){
                    $this->data['paying'] = [];
                    foreach($request->paying as $pay){
                        $this->data['paying'][] = [
                            'project_id'        => $this->data['model']->id,
                            'project_type_id'   => $pay['pay_id'],
                            'first_pay'         => $pay['first_pay'],
                            'number_id'         => $pay['number_pays'],
                            'notes_ar'          => $pay['note_pays_ar'],
                            'notes_en'          => $pay['note_pays_en'],
                            'notes_fa'          => $pay['note_pays_fa'],
                            // 'notes_tr'          => $pay['note_pays_tr'],
                        ];
                    }
                    if(!empty($this->data['paying'])){
                        PayingMethod::insert($this->data['paying']);
                    }
                }
                if(!empty($request->prices)){
                    $this->data['prices'] = [];
                    foreach($request->prices as $price){
                        $this->data['prices'][] = [
                            'project_id'        => $this->data['model']->id,
                            'balance_id'        => $price['balance'],
                            'cat_id'            => $price['projecttypeprice'],
                            'is_sold'           => $price['is_sold'],
                            'room_number'       => $price['room'],
                            'salons_number'     => $price['salon'],
                            // 'bathes_number'     => $price['bath'],
                            'lowest_area'       => $price['lowest_area'],
                            'highest_area'      => $price['highest_area'],
                            'lowest_price'      => $price['lowest_price'],
                            'highest_price'     => $price['highest_price'],
                            'discount'          => $price['discount'],
                            
                        ];
                    }
                    if(!empty($this->data['prices'])){
                        Price::insert($this->data['prices']);
                    }
                }
                // images

                 if (is_array($request->slider_images) && !empty($request->slider_images)) {
                    $attachments = Attachment::whereIn('id', $request->slider_images)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }


                if (is_array($request->featured_images) && !empty($request->featured_images)) {
                    $attachments = Attachment::whereIn('id', $request->featured_images)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->image_internal) && !empty($request->image_internal)) {
                    $attachments = Attachment::whereIn('id', $request->image_internal)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->image_external) && !empty($request->image_external)) {
                    $attachments = Attachment::whereIn('id', $request->image_external)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->image_layouts) && !empty($request->image_layouts)) {
                    $attachments = Attachment::whereIn('id', $request->image_layouts)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->ser_and_fac) && !empty($request->ser_and_fac)) {
                    $attachments = Attachment::whereIn('id', $request->ser_and_fac)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                $this->updatePrices($this->data['model']->id);
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
                if(array_key_exists('projectImagePath1'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'projectImagePath1'.$locale});
                if(array_key_exists('projectImagePath2'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'projectImagePath2'.$locale});
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
        $this->data['model']                    = CrudModel::withDisabled()->with([
            'categories' => function($q){
                $q->CategoryType(['contracts','property_classifications','property_status','property_features','facilities','payments']);
            },
            'contents' => function($q){
                $q->ContentType(['agents','currencies']);
            },
            'translations','country.translations','city.translations',
            'area.translations','areas.translations','tags.translations','payments','prices'])
            ->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        // project categories
        $this->data['projectCategories']        = Category::with('translations')->whereIn('type',['contracts','property_classifications','property_status','property_features','facilities','payments'])->get();
        $this->data['contracts']                = clone $this->data['projectCategories'];
        $this->data['propertyStatus']           = clone $this->data['projectCategories'];
        $this->data['propertyFeatures']         = clone $this->data['projectCategories'];
        $this->data['propertyOffers']           = clone $this->data['projectCategories'];
        $this->data['facilities']               = clone $this->data['projectCategories'];
        // selected categories
        $this->data['allModelCategories']       = $this->data['model']->categories();
        // $this->data['allModelCategories']       = $this->data['model']->categories;
        $this->data['modelContracts']           = clone $this->data['allModelCategories'];
        $this->data['modelClassifications']     = clone $this->data['allModelCategories'];
        $this->data['modelProjectType']         = clone $this->data['allModelCategories'];
        $this->data['modelPropertyStatus']      = clone $this->data['allModelCategories'];
        $this->data['modelPropertyFeatures']    = clone $this->data['allModelCategories'];
        $this->data['modelPropertyOffers']      = clone $this->data['allModelCategories'];
        $this->data['modelFacilities']          = clone $this->data['allModelCategories'];
        // project contents
        $this->data['projectContents']          = Content::where('type',['agents','currencies'])->get();
        $this->data['agents']                   = clone $this->data['projectContents'];
        // selected contents
        $this->data['allModelContents']         = $this->data['model']->contents();
        $this->data['modelAgents']              = clone $this->data['allModelContents'];
        return view('backend::admin.projects.edit', $this->data);
    }
    public function update(Request $request)
    {
        // dd($request->all());
        $this->data['model']        = CrudModel::with('translations')->withDisabled()->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        $rules = [
            'image_ar'              => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'title_ar'              => 'required|string|max:191',
            'landing_page_title_ar' => 'nullable|string|max:255',
            'landing_page_desc_ar'  => 'nullable|string|max:5000',
            'description_ar'        => 'nullable|string|max:20000',
            'details_ar'            => 'nullable|string|max:20000',
            'about_ar'              => 'nullable|string|max:20000',
            'seo_description_ar'    => 'nullable|string|max:20000',
            'trans_keywords_ar'     => 'nullable|string|max:20000',
            'brief_ar'              => 'nullable|string|max:1000',
            'second_title_ar'       => 'nullable|string|max:255',
            'tab_title_ar'          => 'nullable|string|max:255',
            'delivery_date_ar'      => 'nullable|string|max:255',
            'video_ar'              => 'nullable|string|max:255',
            'video2_ar'             => 'nullable|string|max:255',
            'project_image1_ar'     => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'project_image2_ar'     => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'tags_ar'               => 'nullable|array',
            'tags_ar.*'             => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'turkish_nationality'   => 'in:0,1',
            'code'                  => 'required|string|max:191|min:2|unique:be_projects,code,' . $request->model,
            // 'slug'                  => 'required|string|max:191|min:2|unique:be_projects,slug,' . $request->model,
            'slug'                  => ['required','string',new Slug,'max:191','min:3','unique:be_projects,slug,'.$request->model.''],
            'sort_order'            => 'nullable|digits_between:1,9|numeric|max:999999999|min:1',
            'link'                  => 'nullable|string|max:255',
            // 'bulding_date'          => 'nullable|string|max:191',
            'agent'                 => 'nullable|digits_between:1,9|numeric|max:999999999|min:1',
            'categories_id'         => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'project_type'          => 'required|array',
            'project_type.*'        => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'top_type'              => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'offers'                => 'required|array',
            'offers.*'              => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'status'                => 'required|array',
            'status.*'              => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'features'              => 'required|array',
            'features.*'            => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'top_features'          => 'required|array',
            'top_features.*'        => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'top_offers'            => 'required|array',
            'top_offers.*'          => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'country'               => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'city'                  => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'pro_area'              => 'nullable|digits_between:1,9|numeric|max:999999999|min:1',
            'areas'                 => 'nullable|array',
            'areas.*'               => 'nullable|digits_between:1,9|numeric|max:999999999|min:1',
            'sea'                   => 'nullable|string|max:191',
            'pro_city_distance'     => 'nullable|string|max:191',
            'airport'               => 'nullable|string|max:191',
            'school'                => 'nullable|string|max:191',
            'university'            => 'nullable|string|max:191',
            'hospital'              => 'nullable|string|max:191',
            'mosque'                => 'nullable|string|max:191',
            'facilities'            => 'nullable|array',
            'facilities.*'          => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'keywords'              => 'nullable|string|max:10000',
            'disabled_at'           => 'in:yes,no',
            'is_featured'           => 'in:yes,no',
            'is_statistics'         => 'in:yes,no',
        ];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!is_null($request->input('title_'.$locale))){
                    $rules['image_'.$locale]            = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                    $rules['project_image1_'.$locale]   = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                    $rules['project_image2_'.$locale]   = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                    $rules['title_'.$locale]            = 'required|string|max:191';
                    $rules['landing_page_title_'.$locale]   = 'nullable|string|max:255';
                    $rules['landing_page_desc_'.$locale]    = 'nullable|string|max:5000';
                    $rules['description_'.$locale]      = 'required|string|max:20000';
                    $rules['details_'.$locale]          = 'required|string|max:20000';
                    $rules['about_'.$locale]            = 'required|string|max:20000';
                    $rules['brief_'.$locale]            = 'required|string|max:1000';
                    $rules['second_title_'.$locale]     = 'nullable|string|max:255';
                    $rules['tab_title_'.$locale]        = 'nullable|string|max:255';
                    $rules['seo_description_'.$locale]  = 'nullable|string|max:20000';
                    $rules['trans_keywords_'.$locale]   = 'nullable|string|max:20000';
                    $rules['delivery_date_'.$locale]    = 'nullable|string|max:255';
                    $rules['video_'.$locale]            = 'nullable|string|max:255';
                    $rules['video2_'.$locale]           = 'nullable|string|max:255';
                    $rules['tags_'.$locale]             = 'nullable|array';
                    $rules['tags_'.$locale.'.*']        = 'nullable|digits_between:1,9|numeric|max:999999999|min:1';
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
        // forms repeates validation
        if(!empty($request->paying)){
            $rules['paying.*.pay_id']           = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['paying.*.first_pay']        = 'required|string|max:191';
            $rules['paying.*.number_pays']      = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['paying.*.note_pays_ar']     = 'nullable|string|max:5000';
            $rules['paying.*.note_pays_en']     = 'nullable|string|max:5000';
            $rules['paying.*.note_pays_fa']     = 'nullable|string|max:5000';
            // $rules['paying.*.note_pays_tr']     = 'nullable|string|max:5000';
        }
        if(!empty($request->prices)){
            $rules['prices.*.balance']          = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['prices.*.projecttypeprice'] = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['prices.*.is_sold']          = 'nullable|string|max:10|min:1';
            $rules['prices.*.room']             = 'nullable|numeric|max:999|min:0';
            $rules['prices.*.salon']            = 'nullable|numeric|max:999999999|min:0';
            // $rules['prices.*.bath']             = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.lowest_area']      = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.highest_area']     = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.lowest_price']     = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.highest_price']    = 'nullable|numeric|max:999999999|min:0';
            $rules['prices.*.discount']         = 'nullable|numeric|max:100|min:0';
            
        }
        // new forms repeates validation
        if(!empty($request->new_paying)){
            $rules['new_paying.*.pay_id']           = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['new_paying.*.first_pay']        = 'required|string|max:191';
            $rules['new_paying.*.number_pays']      = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['new_paying.*.note_pays_ar']     = 'nullable|string|max:5000';
            $rules['new_paying.*.note_pays_en']     = 'nullable|string|max:5000';
            $rules['new_paying.*.note_pays_fa']     = 'nullable|string|max:5000';
            // $rules['new_paying.*.note_pays_tr']     = 'nullable|string|max:5000';

        }
        if(!empty($request->new_prices)){
            $rules['new_prices.*.balance']          = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['new_prices.*.projecttypeprice'] = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['new_prices.*.is_sold']          = 'nullable|string|max:10|min:1';
            $rules['new_prices.*.room']             = 'nullable|numeric|max:999|min:0';
            $rules['new_prices.*.salon']            = 'nullable|numeric|max:999999999|min:0';
            // $rules['new_prices.*.bath']             = 'nullable|numeric|max:999999999|min:0';
            $rules['new_prices.*.lowest_area']      = 'nullable|numeric|max:999999999|min:0';
            $rules['new_prices.*.highest_area']     = 'nullable|numeric|max:999999999|min:0';
            $rules['new_prices.*.lowest_price']     = 'nullable|numeric|max:999999999|min:0';
            $rules['new_prices.*.highest_price']    = 'nullable|numeric|max:999999999|min:0';
            $rules['new_prices.*.discount']         = 'nullable|numeric|max:100|min:0';
            
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
                $this->data['model']->code                  = $request->code;
                $this->data['model']->slug                  = $request->slug;
                $this->data['model']->sort_order            = $request->sort_order;
                if($request->company_and_project == 'yes'){
                    $this->data['model']->link                  = $request->link;
                }
                // $this->data['model']->establishment_date    = $request->bulding_date;
                $this->data['model']->country_id            = $request->country;
                $this->data['model']->city_id               = $request->city;
                $this->data['model']->sea                   = $request->sea;
                $this->data['model']->area_id               = $request->pro_area;
                $this->data['model']->city_distance         = $request->pro_city_distance;
                $this->data['model']->airport               = $request->airport;
                $this->data['model']->school                = $request->school;
                $this->data['model']->university            = $request->university;
                $this->data['model']->hospital              = $request->hospital;
                $this->data['model']->mosque                = $request->mosque;
                $this->data['model']->mall                  = $request->mall;
                $this->data['model']->turkish_nationality   = $request->turkish_nationality;
                $this->data['model']->keywords              = $request->keywords;
                if($request->disabled_at == 'yes'){
                    $this->data['model']->disabled_at       = NULL;
                }else{
                    $this->data['model']->disabled_at       = \Carbon\Carbon::now()->toDateTimeString();
                }
                $this->data['model']->is_featured           = $request->is_featured;
                $this->data['model']->is_statistics         = $request->is_statistics;
               
                $tags = collect();
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('title_'.$locale))){
                        ${'imagePath'.$locale} = null;
                        if($request->hasFile('image_'.$locale)){
                            ${'imagePath'.$locale}                          = $request->file('image_'.$locale)->store('projects');
                            $this->data['model']->{'image:'.$locale}        = ${'imagePath'.$locale};
                        }
                        $this->data['model']->{'title:'.$locale}            = $request->input('title_'.$locale);
                        $this->data['model']->{'landing_page_title:'.$locale}   = $request->input('landing_page_title_'.$locale);
                        $this->data['model']->{'landing_page_desc:'.$locale}    = $request->input('landing_page_desc_'.$locale);
                        $this->data['model']->{'description:'.$locale}      = $request->input('description_'.$locale);
                        $this->data['model']->{'details:'.$locale}          = $request->input('details_'.$locale);
                        $this->data['model']->{'about:'.$locale}            = $request->input('about_'.$locale);
                        $this->data['model']->{'brief:'.$locale}            = $request->input('brief_'.$locale);
                        if($request->company_and_project == 'yes'){
                            $this->data['model']->{'second_title:'.$locale}     = $request->input('second_title_'.$locale);
                        }
                        $this->data['model']->{'tab_title:'.$locale}        = $request->input('tab_title_'.$locale);

                        $this->data['model']->{'seo_description:'.$locale}  = $request->input('seo_description_'.$locale);
                        $this->data['model']->{'trans_keywords:'.$locale}   = $request->input('trans_keywords_'.$locale);
                        $this->data['model']->{'delivery_date:'.$locale}    = $request->input('delivery_date_'.$locale);
                        $this->data['model']->{'video:'.$locale}            = $request->input('video_'.$locale);
                        $this->data['model']->{'video2:'.$locale}           = $request->input('video2_'.$locale);
                        ${'projectImagePath1'.$locale} = null;
                        if($request->hasFile('project_image1_'.$locale)){
                            ${'projectImagePath1'.$locale}                              = $request->file('project_image1_'.$locale)->store('projects');
                            $this->data['model']->{'project_image1:'.$locale}   = ${'projectImagePath1'.$locale};
                        }
                        ${'projectImagePath2'.$locale} = null;
                        if($request->hasFile('project_image2_'.$locale)){
                            ${'projectImagePath2'.$locale}                              = $request->file('project_image2_'.$locale)->store('projects');
                            $this->data['model']->{'project_image2:'.$locale}   = ${'projectImagePath2'.$locale};
                        }
                        $tags = $tags->merge($request->{'tags_'.$locale});
                    }
                }
                $this->data['model']->save();
                // save tags
                $this->data['model']->tags()->sync($tags->unique());
                // save project categories
                $projectCategories     =   [];
                if(!empty($request->offers)){
                    foreach($request->offers as $offer)
                    {
                        $projectCategories[$offer] = ['options' => in_array($offer, $request->top_offers) ? 'top_offer' : null];
                    }
                }
                if(!empty($request->categories_id))
                {
                    $projectCategories[$request->categories_id] = [];
                }
                if(!empty($request->project_type)){
                    foreach($request->project_type as $projectType)
                    {
                        $projectCategories[$projectType] = ['options' => $projectType == $request->top_type ? 'top_type' : null];
                    }
                }
                if(!empty($request->status)){
                    foreach($request->status as $stat)
                    {
                        $projectCategories[$stat] = [];
                    }
                }
                if(!empty($request->features)){
                    foreach($request->features as $feature)
                    {
                        $projectCategories[$feature] = ['options' => in_array($feature, $request->top_features) ? 'top_feature' : null];
                    }
                }
                if(!empty($request->facilities)){
                    foreach($request->facilities as $facility)
                    {
                        $projectCategories[$facility] = [];
                    }
                }
                if(!empty($projectCategories)){
                    $this->data['model']->categories()->sync($projectCategories);
                }
                // save projec contents
                $projectContents     =   [];
                if (!empty($request->agent)) {
                    $projectContents[]      = $request->agent;
                    $this->data['model']->contents()->sync($projectContents);
                }else{
                    $this->data['model']->contents()->sync($projectContents);
                }
                // if(!empty($projectContents)){
                //     $this->data['model']->contents()->sync($projectContents);
                // }
                // save project areas
                if (!empty($request->areas)) {
                    $this->data['model']->areas()->sync($request->areas);
                }
                // forms repeates
                if($request->paying){
                    foreach($request->paying as $key=>$item){
                        $update_payng                       = PayingMethod::findOrFail($key);
                        if(!empty($update_payng)){
                            $update_payng->project_type_id  = $item['pay_id'];
                            $update_payng->first_pay        = $item['first_pay'];
                            $update_payng->number_id        = $item['number_pays'];
                            $update_payng->notes_ar         = $item['note_pays_ar'];
                            $update_payng->notes_en         = $item['note_pays_en'];
                            $update_payng->notes_fa         = $item['note_pays_fa'];
                            // $update_payng->notes_tr         = $item['note_pays_tr'];
                            $update_payng->save();
                        }
                    }
                }
                if(!empty($request->new_paying)){
                    $this->data['new_paying'] = [];
                    foreach($request->new_paying as $pay){
                        $this->data['new_paying'][] = [
                            'project_id'        => $this->data['model']->id,
                            'project_type_id'   => $pay['pay_id'],
                            'first_pay'         => $pay['first_pay'],
                            'number_id'         => $pay['number_pays'],
                            'notes_ar'          => $pay['note_pays_ar'],
                            'notes_en'          => $pay['note_pays_en'],
                            'notes_fa'          => $pay['note_pays_fa'],
                            // 'notes_tr'          => $pay['note_pays_tr'],
                        ];
                    }
                    if(!empty($this->data['new_paying'])){
                        PayingMethod::insert($this->data['new_paying']);
                    }
                }
                if($request->prices){
                    foreach($request->prices as $key=>$item){
                        $update_price                       = Price::findOrFail($key);
                        if(!empty($update_price)){
                            $update_price->balance_id       = $item['balance'];
                            $update_price->cat_id           = $item['projecttypeprice'];
                            $update_price->is_sold          = $item['is_sold'];
                            $update_price->room_number      = $item['room'];
                            $update_price->salons_number    = $item['salon'];
                            // $update_price->bathes_number    = $item['bath'];
                            $update_price->lowest_area      = $item['lowest_area'];
                            $update_price->highest_area     = $item['highest_area'];
                            $update_price->lowest_price     = $item['lowest_price'];
                            $update_price->highest_price    = $item['highest_price'];
                            $update_price->discount         = $item['discount'];
                            $update_price->save();
                        }
                    }
                }
                if(!empty($request->new_prices)){
                    $this->data['new_prices'] = [];
                    foreach($request->new_prices as $price){
                        $this->data['new_prices'][] = [
                            'project_id'        => $this->data['model']->id,
                            'balance_id'        => $price['balance'],
                            'cat_id'            => $price['projecttypeprice'],
                            'is_sold'           => $price['is_sold'],
                            'room_number'       => $price['room'],
                            'salons_number'     => $price['salon'],
                            // 'bathes_number'     => $price['bath'],
                            'lowest_area'       => $price['lowest_area'],
                            'highest_area'      => $price['highest_area'],
                            'lowest_price'      => $price['lowest_price'],
                            'highest_price'     => $price['highest_price'],
                            'discount'          => $price['discount'],
                            
                        ];
                    }
                    if(!empty($this->data['new_prices'])){
                        Price::insert($this->data['new_prices']);
                    }
                }
                // images

                
                   if (is_array($request->slider_images) && !empty($request->slider_images)) {
                    $attachments = Attachment::whereIn('id', $request->slider_images)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->featured_images) && !empty($request->featured_images)) {
                    $attachments = Attachment::whereIn('id', $request->featured_images)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->image_internal) && !empty($request->image_internal)) {
                    $attachments = Attachment::whereIn('id', $request->image_internal)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->image_external) && !empty($request->image_external)) {
                    $attachments = Attachment::whereIn('id', $request->image_external)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->image_layouts) && !empty($request->image_layouts)) {
                    $attachments = Attachment::whereIn('id', $request->image_layouts)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                if (is_array($request->ser_and_fac) && !empty($request->ser_and_fac)) {
                    $attachments = Attachment::whereIn('id', $request->ser_and_fac)->get();

                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $this->data['model']->id;
                        $attachment->attachable_type    = get_class($this->data['model']);
                        $attachment->save();
                    }
                }
                $this->updatePrices($this->data['model']->id);
            });
        } catch (\Exception $e) {
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('imagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'imagePath'.$locale});
                if(array_key_exists('projectImagePath1'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'projectImagePath1'.$locale});
                if(array_key_exists('projectImagePath2'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'projectImagePath2'.$locale});
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
    public function destroy(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->withTrashed()->findOrFail($request->model);
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
        $this->authorize('forceDelete', $this->data['model']);
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->forceDelete();
                if($this->data['model']->image) app()->ImageManipulator->deleteImage($this->data['model']->image);
                if($this->data['model']->project_image1) app()->ImageManipulator->deleteImage($this->data['model']->project_image1);
                if($this->data['model']->project_image2) app()->ImageManipulator->deleteImage($this->data['model']->project_image2);
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
    public function destroyTranslation(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->withTrashed()->with('translations')->findOrFail($request->model);
        $this->authorize('deleteTranslation', $this->data['model']);
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
        $this->data['models'] = CrudModel::withDisabled()->withTrashed()->whereIn('id', explode(',', $request->ids))->get();
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
                        if($model->image) app()->ImageManipulator->deleteImage($model->image);
                        if($model->project_image1) app()->ImageManipulator->deleteImage($model->project_image1);
                        if($model->project_image2) app()->ImageManipulator->deleteImage($model->project_image2);
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
        $this->data['models']       = CrudModel::withDisabled()->onlyTrashed()->whereIn('id', explode(',', $request->ids))->get();
        $this->data['attribute']    = 'full_name';
        $this->data['failed']       = collect([]);
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
    public function deletePayment(Request $request)
    {
        $this->data['model'] = PayingMethod::findOrFail($request->payment_id);
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
    public function deletePrice(Request $request)
    {
        $this->data['model'] = Price::findOrFail($request->price_id);
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
    public function disable(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail($request->model);
        $this->authorize('disable', $this->data['model']);
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
        $this->authorize('enable', $this->data['model']);
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
    public function special(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->findOrFail($request->model);
        $this->authorize('special', $this->data['model']);
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->is_special = 1;
                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.special_error.title'),
                'description' => __('cms::messages.special_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.special_success.title'),
            'description' => __('cms::messages.special_success.description')
        ]);
    }
    public function notSpecial(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->findOrFail($request->model);
        $this->authorize('not_special', $this->data['model']);
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->is_special = 0;
                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.not_special_error.title'),
                'description' => __('cms::messages.not_special_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.not_special_success.title'),
            'description' => __('cms::messages.not_special_success.description')
        ]);
    }
    public function sold(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->is_sold = 1;
                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.sold_error.title'),
                'description' => __('cms::messages.sold_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.sold_success.title'),
            'description' => __('cms::messages.sold_success.description')
        ]);
    }
    public function notSold(Request $request)
    {
        $this->data['model'] = CrudModel::withDisabled()->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        try {
            DB::transaction(function() use ($request) {
                $this->data['model']->is_sold = 0;
                $this->data['model']->save();
            });
        } catch (\Exception $e) {
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.not_sold_error.title'),
                'description' => __('cms::messages.not_sold_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.not_sold_success.title'),
            'description' => __('cms::messages.not_sold_success.description')
        ]);
    }
    public function copy(Request $request)
    {
        
        $this->data['model'] = CrudModel::with([
            'translations',
            'areas',
            'area',
            'area.customFields',
            'area.city',
            'area.city.customFields',
            'contents.CustomFields',
            'contents',
            'payments.payCategory',
            'payments.numberCategory',
            'attachments' => function($qu) {
                $qu->orderBy('input_name');
            },
            'tags' => function($tag) {
                $tag->with('translations');
            },
            'prices' => function($query) {
                $query->orderBy('lowest_price');
            },
            'allCategories' => function($q) {
                $q->whereIn('type', [
                    'property_classifications',
                    'contracts',
                    'propertyFeatures',
                    'facilities',
                    'property_features',
                    'property_status'
                ]);
            }
        ])->findOrFail($request->model);
        
        
        $this->authorize('update', $this->data['model']);
        try {
           
            DB::transaction(function() {
                $this->data['new_model']                        = new CrudModel;
                $this->data['new_model']->code                  = $this->data['model']->code . mt_rand(1, 9999);
                $this->data['new_model']->slug                  = $this->data['model']->slug . mt_rand(1, 9999);
                $this->data['new_model']->sort_order            = $this->data['model']->sort_order;
                $this->data['new_model']->link                  = $this->data['model']->link;
                if(!empty($this->data['model']->country)){
                    $this->data['new_model']->country_id            = $this->data['model']->country->id;
                }
                if(!empty($this->data['model']->city)){
                    $this->data['new_model']->city_id               = $this->data['model']->city->id;
                }
                $this->data['new_model']->sea                   = $this->data['model']->sea;
                $this->data['new_model']->area_id               = $this->data['model']->pro_area;
                $this->data['new_model']->city_distance         = $this->data['model']->pro_city_distance;
                $this->data['new_model']->airport               = $this->data['model']->airport;
                $this->data['new_model']->school                = $this->data['model']->school;
                $this->data['new_model']->university            = $this->data['model']->university;
                $this->data['new_model']->hospital              = $this->data['model']->hospital;
                $this->data['new_model']->mosque                = $this->data['model']->mosque;
                $this->data['new_model']->mall                  = $this->data['model']->mall;
                $this->data['new_model']->turkish_nationality   = $this->data['model']->turkish_nationality;
                $this->data['new_model']->keywords              = $this->data['model']->keywords;
                $this->data['new_model']->disabled_at           = $this->data['model']->disabled_at;
                $this->data['new_model']->is_featured           = $this->data['model']->is_featured;
                $this->data['new_model']->is_statistics         = $this->data['model']->is_statistics;

                foreach($this->data['model']->translations as $trans){
                    $locale                                                     = $trans->locale;
                    // $contents = Storage::url($trans->image);
                    // dd($contents);
                    // ${'imagePath'.$locale}                                      = null;
                    // if(!empty($trans->image)){
                    //     ${'imagePath'.$locale}                                  = Storage::get($trans->image)->store('projects');
                    //     $this->data['new_model']->{'image:'.$locale}            = ${'imagePath'.$locale};
                    // }

                    $this->data['new_model']->{'title:'.$locale}                = $trans->title;
                    $this->data['new_model']->{'landing_page_title:'.$locale}   = $trans->landing_page_title;
                    $this->data['new_model']->{'landing_page_desc:'.$locale}    = $trans->landing_page_desc;
                    $this->data['new_model']->{'description:'.$locale}          = $trans->description;
                    $this->data['new_model']->{'details:'.$locale}              = $trans->details;
                    $this->data['new_model']->{'about:'.$locale}                = $trans->about;
                    $this->data['new_model']->{'brief:'.$locale}                = $trans->brief;
                    $this->data['new_model']->{'second_title:'.$locale}         = $trans->second_title;
                    $this->data['new_model']->{'tab_title:'.$locale}            = $trans->tab_title;
                    $this->data['new_model']->{'seo_description:'.$locale}      = $trans->seo_description;
                    $this->data['new_model']->{'trans_keywords:'.$locale}       = $trans->trans_keywords;
                    $this->data['new_model']->{'delivery_date:'.$locale}        = $trans->delivery_date;
                    $this->data['new_model']->{'video:'.$locale}                = $trans->video;
                    $this->data['new_model']->{'video2:'.$locale}               = $trans->video2;
                    
                    // ${'projectImagePath1'.$locale} = null;
                    // if($request->hasFile('project_image1_'.$locale)){
                    //     ${'projectImagePath1'.$locale}                              = $request->file('project_image1_'.$locale)->store('projects');
                    //     $this->data['new_model']->{'project_image1:'.$locale}       = ${'projectImagePath1'.$locale};
                    // }
                    // ${'projectImagePath2'.$locale} = null;
                    // if($request->hasFile('project_image2_'.$locale)){
                    //     ${'projectImagePath2'.$locale}                              = $request->file('project_image2_'.$locale)->store('projects');
                    //     $this->data['new_model']->{'project_image2:'.$locale}   = ${'projectImagePath2'.$locale};
                    // }

                }
                
                $this->data['new_model']->save();
                // save tags
                $tags = [];
                if($this->data['model']->tags->isNotEmpty()){
                    foreach($this->data['model']->tags as $tag){
                        foreach($tag->translations as $t_trans){
                            $tags[] = $t_trans->tag_id;
                        }
                    }
                }
                $this->data['new_model']->tags()->sync(array_unique($tags));
                if($this->data['model']->allCategories->isNotEmpty()){
                    $categories_ids       = $this->data['model']->allCategories->pluck('id')->toArray();
                    if(isset($categories_ids) && !empty($categories_ids)){
                        $this->data['new_model']->categories()->sync($categories_ids);
                    }
                }
                
                $paying = PayingMethod::where('project_id',$this->data['model']->id)->get();
                if($paying->isNotEmpty()){
                    $this->data['insert_pay'] = [];
                    foreach($paying as $pay){
                        $this->data['insert_pay'][] = [
                            'project_id'        => $this->data['new_model']->id,
                            'project_type_id'   => $pay->project_type_id,
                            'first_pay'         => $pay->first_pay,
                            'number_id'         => $pay->number_id,
                            'notes_ar'          => $pay->notes_ar,
                            'notes_en'          => $pay->notes_en,
                            'notes_fa'          => $pay->notes_fa,
                        ];
                    }
                    if(!empty($this->data['insert_pay'])){
                        PayingMethod::insert($this->data['insert_pay']);
                    }
                }

                $prices         = Price::where('project_id',$this->data['model']->id)->get();
                if($prices->isNotEmpty()){
                    $this->data['insert_price'] = [];
                    foreach($prices as $price){
                        $this->data['insert_price'][] = [
                            'project_id'        => $this->data['new_model']->id,
                            'balance_id'        => $price->balance_id,
                            'cat_id'            => $price->cat_id,
                            'room_number'       => $price->room_number,
                            'salons_number'     => $price->salons_number,
                            'lowest_area'       => $price->lowest_area,
                            'highest_area'      => $price->highest_area,
                            'lowest_price'      => $price->getRawOriginal('lowest_price'),
                            'highest_price'     => $price->getRawOriginal('highest_price'),
                            'discount'          => $price->discount,
                            
                        ];
                    }
                    if(!empty($this->data['insert_price'])){
                        Price::insert($this->data['insert_price']);
                    }
                }
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return new ResponseHandler([
                'success'     => false,
                'type'        => 'danger',
                'title'       => __('cms::messages.copy_error.title'),
                'description' => __('cms::messages.copy_error.description')
            ]);
        }
        return new ResponseHandler([
            'success'     => true,
            'type'        => 'success',
            'title'       => __('cms::messages.copy_success.title'),
            'description' => __('cms::messages.copy_success.description')
        ]);
    }
    public function requests (Request $request)
    {
        $this->authorize('requests', Tag::class);
        return view('backend::admin.requests', $this->data);
    }
    public function data_requests(Request $request)
    {
        $list = ContactUS::query();
        $datatables = DataTables::of($list);
        $datatables
        ->addIndexColumn()
        ->filter(function($q) use ($request) {
            if(!empty($filter = $request->filter) && is_array($filter)){
                $filter = collect($filter)->mapWithKeys(function ($item) {
                    return [$item['name'] => $item['value']];
                });

                $q->when($filter->contains(function ($value, $key) {
                    return $key == 'daterange' && !empty($value);
                }), function($query) use ($filter) {
                    $since = Carbon::parse(Str::before($filter['daterange'], ' -'))->subDay();
                    $until = Carbon::parse(Str::after($filter['daterange'], '- '));

                    // Prevents entering future dates.
                    if($until > Carbon::now())
                    {
                        $until = Carbon::now();
                    }
                    $query->whereBetween('created_at', [$since->format('Y-m-d')." 00:00:00", $until->format('Y-m-d')." 23:59:59"]);
                });
            }
        })
        // ->addColumn('link', function($model){
        //     $category = $model->category;
        //     return route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'unknown'), 'slug' => $model->slug]);
        // })
        ->addColumn('breef', function($model){
            return $model->description;
        })// Adds an incremental first row.
        ->addColumn('date', function($model){
            return \Modules\Cms\Classes\DateHelper::parseDate($model->created_at);
        })
        ->addColumn('actions', function($model){
            $items = [];
            $actions['dropdown'] = [];
            $actions['icons'] = [];
            // Gives the ROOT user the ability to login with any other user without a password.
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
        // 'breef' is the visitor's message: it is never raw, so the DataProcessor escapes it (S5).
        $rawColumns[] = 'actions';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function request_summary(Request $request)
    {
        $this->data['model'] = ContactUS::findOrFail($request->model);

        $this->data['summary'] = view('backend::admin.summary', $this->data)->render();

        return new ResponseHandler([
            'success'   => true,
            'model'     => $this->data['model'],
            'summary'   => $this->data['summary']
        ]);
    }
    // 
    public function properties (Request $request)
    {
        $this->authorize('requests', Tag::class);
        return view('backend::admin.properties.properties', $this->data);
    }
    public function data_properties(Request $request)
    {
        $list = PropertyForm::query();
        $datatables = DataTables::of($list);
        $datatables
        ->addIndexColumn()
        ->filter(function($q) use ($request) {
            if(!empty($filter = $request->filter) && is_array($filter)){
                $filter = collect($filter)->mapWithKeys(function ($item) {
                    return [$item['name'] => $item['value']];
                });

                $q->when($filter->contains(function ($value, $key) {
                    return $key == 'daterange' && !empty($value);
                }), function($query) use ($filter) {
                    $since = Carbon::parse(Str::before($filter['daterange'], ' -'))->subDay();
                    $until = Carbon::parse(Str::after($filter['daterange'], '- '));

                    // Prevents entering future dates.
                    if($until > Carbon::now())
                    {
                        $until = Carbon::now();
                    }
                    $query->whereBetween('created_at', [$since->format('Y-m-d')." 00:00:00", $until->format('Y-m-d')." 23:59:59"]);
                });
            }
        })
        // ->addColumn('link', function($model){
        //     $category = $model->category;
        //     return route('PropertyController@single', ['type' => (!is_null($category) ? $category->slug : 'unknown'), 'slug' => $model->slug]);
        // })
        ->addColumn('property_explanation', function($model){
            return $model->property_explanation;
        })// Adds an incremental first row.
        ->addColumn('date', function($model){
            return \Modules\Cms\Classes\DateHelper::parseDate($model->created_at);
        })
        ->addColumn('actions', function($model){
            $items = [];
            $actions['dropdown'] = [];
            $actions['icons'] = [];
            // Gives the ROOT user the ability to login with any other user without a password.
            $items[] = array_merge($this->actions['edit'], [
                'url'   => route('ProjectController@showDetails', ['model' => $model->id]),
                'id'    => 'edit_' . $model->id
            ]);
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
        $rawColumns[] = 'breef';
        $rawColumns[] = 'actions';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function properties_summary(Request $request)
    {
        $this->data['model'] = PropertyForm::with('attachments')->findOrFail($request->model);

        $this->data['summary'] = view('backend::admin.properties.summary', $this->data)->render();

        return new ResponseHandler([
            'success'   => true,
            'model'     => $this->data['model'],
            'summary'   => $this->data['summary']
        ]);
    }
    public function showDetails(Request $request)
    {
        $this->data['model']    = PropertyForm::with('attachments')->findOrFail($request->model);

        $this->data['filters'] = Category::select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])
        ->orderBy('sort_order','ASC')->with('translations')->get();

        return view('backend::admin.projects.show_details', $this->data);

        
    }
    
}
