<?php
namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Cms\Entities\Category;
use Modules\Frontend\Http\Controllers\FrontendController;
use Modules\Backend\Entities\Project  as CrudModel;
use Illuminate\Support\Str;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\Area;
use Modules\Backend\Entities\Project;
use Modules\Cms\Entities\Attachment;
use Modules\Backend\Entities\PropertyForm;
use Modules\Cms\Entities\FlagCountry;
use Modules\Cms\Classes\ResponseHandler;
use Validator;
use Storage;
use DB;
use Modules\Backend\Entities\Price;
use Modules\Cms\Entities\Content;
use App;


class OpportunityController extends FrontendController
{


    public function prepareFilter(Request $request)
    {   

        $array = [];
        if(!$request->city){
            $array += array('city' => request('city', 'turkey'));
        }else{
            $array += array('city' => request('city', $request->city));
        }

        if(!$request->opportunity_classification){
            $array += array('opportunity_classification' =>  'properties');
        }elseif($request->opportunity_classification == 'all'){
            $array += array('opportunity_classification' => "all_properties");
        }else{
            $array += array('opportunity_classification' => request('opportunity_classification', $request->property_classification));
        }

        if(!$request->contract){
            $array += array('contract' =>  'all');
        }elseif($request->contract == 'all'){
            $array += array('contract' => "all_contract");
        }else{
            $array += array('contract' => request('contract', $request->contract));
        }
// return $array;
        return redirect(route('OpportunityController@filter', array_merge($request->except('_token'), $array)));
    }

    public function processFilter(Request $request)
    {   
        $this->data['OpportunityClassification']  = $request->opportunity_classification;
        $this->data['contract']                 = $request->contract;
        $this->data['city']                     = $request->city;
        $this->data['area']                     = $request->area;
        $this->data['params']                   = $request->all();

        $supportedLocales = collect(\LaravelLocalization::getLocalesOrder())->keys()->map(function($supportedLocale) {
            return '/' . $supportedLocale . '/';
        })->toArray();

        $filterPageExists = Content::translatedIn(app()->getLocale())
        ->where('type', 'filters')
        ->with('translations')
        ->get();
// dd($filterPageExists);
        $filterPageExists = $filterPageExists->filter(function($item) use ($supportedLocales, $request) {

            $parsedFilterLink = parse_url(str_replace($supportedLocales, '/', $request->filter_url));
            $parsedItemLink = parse_url(str_replace($supportedLocales, '/', $item->link));
// dd($parsedItemLink);
            $parsedFilterQuery = [];
            $parsedItemQuery = [];

            if(isset($parsedFilterLink['query']) && isset($parsedItemLink['query']))
            {
                parse_str($parsedFilterLink['query'], $parsedFilterQuery);
                parse_str($parsedItemLink['query'], $parsedItemQuery);
            }

            unset($parsedFilterQuery['sort']);
            unset($parsedItemQuery['sort']);
// dd( $parsedFilterLink['query'] , $parsedItemLink['query'] );

            return $parsedFilterLink['path'] == $parsedItemLink['path'] && $parsedFilterQuery == $parsedItemQuery;
        })->first();
// dd($filterPageExists);
         $areas          = collect([]);
// dd(request()->city);
          if(request()->city != 'turkey'){
        $city = City::where('native_name', request()->city)->with('translations')->first();
    }else{
        $city = '';
    }
        $area = Area::where('native_name', request()->area)->with('translations')->first();

        if(request()->opportunity_classification != 'properties' && request()->opportunity_classification != 'all_properties'){
            $opportunityClassification = Category::where('slug', request()->opportunity_classification)->firstOrFail();
        }else{
            $opportunityClassification = ''; 
        }
        if(request()->contract != 'all'   &&  request()->contract != 'all_contract'){
            $contract = Category::where('slug', request()->contract)->firstOrFail();
        }else{
            $contract = ''; 
        }

        // if ($city->native_name !='turkey')
        //     $areas = Area::where('city_id', $city->id)->with('translations')->get();

        $installment = null;
        if(isset($this->data['params']['installment'])){
            $installment = $this->data['params']['installment'];
        }
        $payment        = isset($this->data['params']['payment'])             ?  $this->data['params']['payment'] : null;
        $this->data['installments']         = collect();
        if (isset($installment) && !empty($installment) ){
            $this->data['installments']     = Category::whereIn('slug',$installment)->with('translations')->get();
        }

        $this->data['rooms']        = isset($this->data['params']['rooms'])         ?  $this->data['params']['rooms'] : null;
        $this->data['living']       = isset($this->data['params']['living'])        ?  $this->data['params']['living'] : null;
        $this->data['facilities']   = isset($this->data['params']['facilities'])    ?  $this->data['params']['facilities']: null;
        $this->data['features']     = isset($this->data['params']['features'])    ?  $this->data['params']['features']: null;
        $this->data['discount']     = isset($this->data['params']['discount'])    ?  $this->data['params']['discount']: null;


// dd($this->data['params']['rooms']);

        $this->data['properties'] = Project::whereHas('city', function ($query) use ($city) {
            if($city != '') $query->where('native_name', $city->native_name);
        })
        ->when($area, function ($query) use ($area) {
            $query->where(function($q) use ($area) {
                $q->whereHas('area', function ($qu) use ($area) {
                    $qu->where('native_name', $area->native_name);
                });
            });
        })
        ->whereHas('opportunityClassifications', function ($query) use ($opportunityClassification) {
            if($opportunityClassification != '') $query->where('slug', $opportunityClassification->slug);
        })
        ->when(request()->property_type, function ($query, $propertyType) {
            $query->whereHas('propertyType', function ($query) use ($propertyType) {
                $query->where('slug', $propertyType);
            });
        })
        ->whereHas('contracts', function ($query) use ($contract) {
            if($contract != '') $query->where('slug', $contract->slug);
        })
        ->when(request()->features,function ($query) {
            $query->whereHas('propertyFeatures', function ($query){
                $query->whereIn('slug', $this->data['features']);
            });
        })
        ->when(request()->discount,function ($query) {
            $query->whereHas('price');
        })
        ->when(request()->facilities,function ($query) {
            $query->whereHas('facilities', function ($query) {
                $query->whereIn('slug', $this->data['facilities']);
            });
        })
        ->when(request()->property_status,function ($query, $property_status) {
            $query->whereHas('propertyStatues', function ($query) use ($property_status){
                $query->whereIn('slug', explode(',', $property_status));
            });
        })
        ->when(request()->featured, function ($query) {
            $query->where('is_special', 1);
        })
        ->when(request()->payment, function ($query, $payment) {
            $paymentCategory = Category::where('slug', $payment)->first();

            if(!empty($paymentCategory)){
                $query->whereHas('payments',function ($query) use ($paymentCategory) {
                    $query->where('project_type_id', $paymentCategory->id);
                });
            }
        })
        ->when(request()->project_code, function ($query, $projectCode) {
            $query->where('code', $projectCode);
        })
        ->leftJoin('price', 'price.project_id', 'be_projects.id')
        ->select('be_projects.id as id',
            'be_projects.created_at',
            'be_projects.slug as slug',
            'be_projects.image as image',
            'be_projects.city_id',
            'be_projects.country_id',
            'be_projects.area_id',
            'be_projects.roi',
            \DB::raw("MIN(price.lowest_price) AS min_price"),
            \DB::raw("MIN(price.lowest_area) AS min_area"),
            \DB::raw("MIN(price.highest_area) AS max_area"),
            \DB::raw("MIN(price.room_number) AS room_number")
        )
        ->groupBy(
            'be_projects.id',
            'be_projects.created_at',
            'slug',
            'image',
            'be_projects.city_id',
            'be_projects.country_id',
            'be_projects.area_id'
        );
        $this->data['properties'] = $this->data['properties']
        ->when(request()->min_price, function ($query, $minPrice) {
            $query->where('price.lowest_price', '<=', $minPrice);
        })
        ->when(request()->max_price, function ($query, $maxPrice) {
            $query->where('price.highest_price', '>=', $maxPrice);
        })
        ->when(request()->min_area, function ($query, $minArea) {
            $query->where('price.lowest_area', '<=', $minArea);
        })
        ->when(request()->max_area, function ($query, $maxArea) {
            $query->where('price.highest_area', '>=', $maxArea);
        })
        ->when(request()->rooms,function ($query) {
// dd($this->data['rooms']);
            $query->whereIn('price.room_number', $this->data['rooms']);
        })
        ->when(request()->living,function ($query) {
            $query->whereIn('price.salons_number', $this->data['living']);
        })
        ->when(request()->installment,function ($query)  {
            $query->whereHas('payments',function($q) {
                $q->whereHas('payCategory',function($qu) {
                    if($this->data['installments']->isNotEmpty()){
                        $qu->whereIn('number_id',$this->data['installments']->pluck('id')->toArray());
                    }
                });
            });
        })
        ->when(request()->sort, function ($query, $sort) {
            switch ($sort){
                case 'date_desc':
                $query->orderBy('be_projects.created_at', 'DESC');
                break;
                case 'date_asc':
                $query->orderBy('be_projects.created_at', 'ASC');
                break;
                case 'price_asc':
                $query->orderBy('min_price', 'ASC');
                break;
                case 'price_desc':
                $query->orderBy('min_price', 'DESC');
                break;
                case 'area_asc':
                $query->orderBy('lowest_area', 'ASC');
                break;
                case 'area_desc':
                $query->orderBy('lowest_area', 'DESC');
                break;
            }
        })
        ->translatedIn(app()->getLocale())
        ->with([
            'translations',
            'city.translations',
            'area',
            'attachments',
            'categories' => function($query){
                $query->whereIn('type', ['contracts', 'opportunity_classifications']);
                $query->with('translations');
            }
        ])->paginate(6);
// dd($opportunityClassification);
        $opportunityClassificationTitle    = !empty($opportunityClassification) ? $opportunityClassification->translateOrFirst(app()->getLocale())->title : '';
        $propertyTypeTitle              = !empty($property_type) ? $property_type->translateOrFirst(app()->getLocale())->title : '';
        $featureTitle                   = !empty($feature) ? $feature->translateOrFirst(app()->getLocale())->title : '';
        $contractTitle                  = !empty($contract) ? $contract->translateOrFirst(app()->getLocale())->title : '';
        $cityName                       = !empty($city) ? $city->translateOrFirst(app()->getLocale())->name : __('frontend::listing.turkey');
        $areaName                       = !empty($area) ? $area->translateOrFirst(app()->getLocale())->name : '';

        $show_f = '';
        $this->data['feat'] = Category::orderBy('sort_order','ASC')->where('type','property_features')->with('translations')->get();
        if($this->data['feat']->isNotEmpty()){
            foreach($this->data['feat'] as $f){
                if(!empty($this->data['features'])){
                    foreach($this->data['features'] as $Q){
                        if($Q == $f->slug){
                            $show_f = $f->translateOrFirst(app()->getLocale())->title;
                        }
                    }
                }
            }
        }

        $autoGeneratedData = [
            'property_type'     => $opportunityClassificationTitle,
            'property_feature'  => $propertyTypeTitle,
            'show_f'            => $show_f,
            'feature'           => $featureTitle,
            'contract'          => $contractTitle,
            'city'              => $cityName,
            'area'              => $areaName
        ];

        $this->data['texts'] = [
            'overview'              => __('frontend::listing.header.overview', $autoGeneratedData),
            'brief'                 => __('frontend::listing.header.brief', $autoGeneratedData),
            'title'                 => __('frontend::listing.header.title', $autoGeneratedData),
            'place_tab_description' => !empty($city) ? $city->translateOrFirst(app()->getLocale())->description : '',
            'city_title'            => __('frontend::listing.header.title', $autoGeneratedData),
        ];

        if(!empty($area))
        {
            $this->data['texts']['place_tab_description'] = $area->translateOrFirst(app()->getLocale())->description;
        }

        if($filterPageExists && !empty($filterPageExists))
        {
            $this->data['texts']['overview']      = $filterPageExists->translateOrFirst(App::getLocale())->description;
            $this->data['texts']['brief']         = $filterPageExists->translateOrFirst(App::getLocale())->brief;
            $this->data['texts']['title']         = $filterPageExists->translateOrFirst(App::getLocale())->title;
            $this->data['texts']['city_title']    = $filterPageExists->translateOrFirst(App::getLocale())->title;


            $title = $filterPageExists->translateOrFirst()->title . ' | ' . __('frontend::main.site_name');
        }
        else
        {
            $title = __('frontend::listing.tab_title', $autoGeneratedData);
            $main_page_keywords = '';
            $main_page_seo = '';
            if(isset($config['main-page-keywords']) && !is_null($config['main-page-keywords']))
            {
                if(!empty($config['main-page-keywords']->translateOrFirst()->description))
                {
                    $main_page_keywords = $config['main-page-keywords']->translateOrFirst()->description;
                }
                else
                {
                    $main_page_keywords = $config['main-page-keywords']->val;
                }
            }
            if(isset($config['main-page-seo']) && !is_null($config['main-page-seo']))
            {
                if(!empty($config['main-page-seo']->translateOrFirst()->description))
                {
                    $main_page_seo = $config['main-page-seo']->translateOrFirst()->description;
                }
                else
                {
                    $main_page_seo = $config['main-page-seo']->val;
                }
            }
        }

// dd($this->data['texts']);
        $this->data['stories'][$this->locale] = \Cache::rememberForever('stories_' . $this->locale, function() {
            return Content::translatedIn($this->locale)->with('translations', 'categories.translations')->where('type','stories')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->get();
        });


        $this->data['view_name'] = 'filter';
        $this->data['featuredProjects_opp'] = Project::select(['be_projects.*',
            DB::raw('
                (
                SELECT trans.title
                FROM be_projects_translations AS trans
                WHERE trans.project_id = be_projects.id
                AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
                ')
        ])->translatedIn(app()->getLocale())->with(['translations' ,'allCategories.translations', 'attachments' => function($query) {
            $query->where('input_name', 'featured_images')->orWhere('input_name', 'image_external');
        } , 'contracts.translations' , 'city.translations', 'area.translations', 'prices' => function($query) {
            $query->orderBy('lowest_price');
        }, 'allCategories.translations','allCategories'])->whereHas('allCategories', function ($query) {
            $query->where('type', 'opportunity_classifications');
        })->orderBy('views', 'DESC')->limit(3)->get();

        return view('frontend::opportunity.index', $this->data);

    }

    public function filter(Request $request)
    {
        $url = request()->fullUrl();

        return $this->processFilter($request->merge(['filter_url' => $url]));
    }



    public function single(Request $request)
    {
        $this->data['model'] = CrudModel::with([
            'translations',
            'areas.translations',
            'area.translations',
            'area.customFields',
            'area.city.translations',
            'area.city.customFields',
            'contents.CustomFields',
            'contents.translations',
            'payments.payCategory.translations',
            'payments.numberCategory.translations',
            'attachments' => function($qu) {
                $qu->orderBy('input_name');
            },
            'tags' => function($tag) {
                $tag->with('translations')->translatedIn(app()->getLocale());
            },
            'prices' => function($query) {
                $query->orderBy('lowest_price');
            },
            'allCategories' => function($q) {
                $q->with('translations')->whereIn('type', [
                    'opportunity_classifications',
                    'contracts',
                    'propertyFeatures',
                    'facilities',
                    'property_features',
                    'property_status'
                ]);
            }
        ])->where('slug', $request->slug)->firstOrFail();

        if(!$this->data['model']->hasTranslation(app()->getLocale()))
        {
            $translatedContentLocales = $this->data['model']->translations()->pluck('locale');
            return self::pageNotTranslated($translatedContentLocales);
        }

        $this->data['category'] = $this->data['model']->allCategories()
        ->where('type', 'opportunity_classifications')->whereNotNull('parent_id')
        ->first();

        $this->data['similarProjects'] = CrudModel::translatedIn(app()->getLocale())->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function($query) {
            $query->orderBy('lowest_price');
        }, 'allCategories' => function($q) {
            $q->where('type', 'opportunity_classifications')->whereNotNull('parent_id');
        },'allCategories.translations'])
        ->whereHas('categories', function($q) use ($request) {
            $q->where('type', 'opportunity_classifications')->whereNotNull('parent_id')->where('slug', $request->type);
        })
        ->inRandomOrder()->limit(4)->get();

        $this->data['meta']   = [
            'meta_title'            => !is_null($this->data['model']->translateOrFirst()->tab_title) ? $this->data['model']->translateOrFirst()->tab_title : $this->data['model']->translateOrFirst()->title,
            'meta_description'      => $this->data['model']->translateOrFirst()->seo_description,
            'meta_keywords'         => $this->data['model']->translateOrFirst()->trans_keywords,
            'meta_og_img'           => $this->data['model']->getTranslatedImage('1000x750'),
            'meta_og_img_width'     => '600' ,
            'meta_og_img_heigh'     => '600' ,
            'meta_og_url'           => route('OpportunityController@single', ['type' => (!is_null($this->data['category']) ? $this->data['category']->slug : 'unknown'), 'slug' => $this->data['model']->slug]),
            'meta_og_type'          => 'Product',
            'meta_og_title'         => !is_null($this->data['model']->translateOrFirst()->tab_title) ? $this->data['model']->translateOrFirst()->tab_title : $this->data['model']->translateOrFirst()->title,

            'meta_twitter_card'     =>'',
            'meta_twitter_title'    => !is_null($this->data['model']->translateOrFirst()->tab_title) ? $this->data['model']->translateOrFirst()->tab_title : $this->data['model']->translateOrFirst()->title,
            'meta_twitter_desc'     => $this->data['model']->translateOrFirst()->seo_description,
            'meta_twitter_img'      => $this->data['model']->getTranslatedImage('1000x750'),
            'meta_twitter_img_src'  => $this->data['model']->getTranslatedImage('1000x750'),
            'meta_og_img_alt'       => $this->data['model']->translateOrFirst()->title,
            'meta_og_desc'          => $this->data['model']->translateOrFirst()->seo_description,
        ];

        $this->data['area'] = $this->data['model']->area;
        $this->data['city'] = !is_null($this->data['area']) ? $this->data['area']->city : null;



        if(!is_null($this->data['area']) && $this->data['area']->customFields->isNotEmpty())
        {
            $this->data['educationStatus'] = $this->data['area']->customFields
            ->filter(function($item) {
                return Str::startsWith($item->key, 'demographic_data.education_status.');
            })
            ->map(function($value, $key) {
                $item = [];
                $item['key']    = $value->key;
                $item['value']  = __('cms::areas.custom_fields.'.$value->key.'.label');
                $item['count']  = $value->value;
                return $item;
            });

            $this->data['peopleData'] = $this->data['area']->customFields
            ->filter(function($item) {
                return Str::startsWith($item->key, 'demographic_data.people_data.');
            });

            $this->data['ageDistribution'] = $this->data['area']->customFields
            ->filter(function($item) {
                return Str::startsWith($item->key, 'demographic_data.age_distribution.');
            })
            ->map(function($value, $key) {
                $item = [];
                $item['key']    = $value->key;
                $item['value']  = __('cms::areas.custom_fields.'.$value->key.'.label');
                $item['count']  = $value->value;
                return $item;
            });

            $this->data['maritalCondition'] = $this->data['area']->customFields
            ->filter(function($item) {
                return Str::startsWith($item->key, 'demographic_data.marital_condition.');
            })
            ->map(function($value, $key) {
                $item = [];
                $item['key']    = $value->key;
                $item['value']  = __('cms::areas.custom_fields.'.$value->key.'.label');
                $item['count']  = $value->value;
                return $item;
            });

            $this->data["educationStatus"]  = json_encode(array_values($this->data["educationStatus"]->toArray()));
            $this->data["ageDistribution"]  = json_encode(array_values($this->data["ageDistribution"]->toArray()));
            $this->data["maritalCondition"] = json_encode(array_values($this->data["maritalCondition"]->toArray()));

            $this->data['priceChangeRentArea'] = $this->data['area']->customFields
            ->filter(function($item) {
                return Str::startsWith($item->key, 'price_change.rent.');
            })
            ->mapWithKeys(function($value, $key) {
                return [Str::after($value->key, 'price_change.rent.') => round($value->value)];
            });

            $this->data['priceChangeRentCity'] = $this->data['city']->customFields
            ->filter(function($item) {
                return Str::startsWith($item->key, 'price_change.rent.');
            })
            ->mapWithKeys(function($value, $key) {
                return [Str::after($value->key, 'price_change.rent.') => round($value->value)];
            });

            if($this->data['priceChangeRentArea']->isNotEmpty() && $this->data['priceChangeRentCity']->isNotEmpty())
            {
                $this->data['priceChangeRentArea'] = $this->data['priceChangeRentArea']->put('category', $this->data['area']->translateOrFirst()->name);
                $this->data['priceChangeRentCity'] = $this->data['priceChangeRentCity']->put('category', $this->data['city']->translateOrFirst()->name);

                $this->data['priceChangeRent'] = array_merge([$this->data['priceChangeRentCity']->toArray()], [$this->data['priceChangeRentArea']->toArray()]);
                $this->data["priceChangeRent"] = json_encode(array_values($this->data["priceChangeRent"]));
            }

            $this->data['priceChangeSaleArea'] = $this->data['area']->customFields
            ->filter(function($item) {
                return Str::startsWith($item->key, 'price_change.sale.');
            })
            ->mapWithKeys(function($value, $key) {
                return [Str::after($value->key, 'price_change.sale.') => round($value->value)];
            });

            $this->data['priceChangeSaleCity'] = $this->data['city']->customFields
            ->filter(function($item) {
                return Str::startsWith($item->key, 'price_change.sale.');
            })
            ->mapWithKeys(function($value, $key) {
                return [Str::after($value->key, 'price_change.sale.') => round($value->value)];
            });

            if($this->data['priceChangeSaleArea']->isNotEmpty() && $this->data['priceChangeSaleCity']->isNotEmpty())
            {
                $this->data['priceChangeSaleArea'] = $this->data['priceChangeSaleArea']->put('category', $this->data['area']->translateOrFirst()->name);
                $this->data['priceChangeSaleCity'] = $this->data['priceChangeSaleCity']->put('category', $this->data['city']->translateOrFirst()->name);

                $this->data['priceChangeSale'] = array_merge([$this->data['priceChangeSaleCity']->toArray()], [$this->data['priceChangeSaleArea']->toArray()]);
                $this->data["priceChangeSale"] = json_encode(array_values($this->data["priceChangeSale"]));
            }
        }

        $lowestPrice = !is_null($priceRange = $this->data['model']->prices->first()) ? $priceRange->getRawOriginal('lowest_price') : 0;


        $this->data['featuredProjects_opp'] = Project::select(['be_projects.*',
            DB::raw('
                (
                SELECT trans.title
                FROM be_projects_translations AS trans
                WHERE trans.project_id = be_projects.id
                AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
                ')
        ])->translatedIn(app()->getLocale())->with(['translations' ,'allCategories.translations', 'attachments' => function($query) {
            $query->where('input_name', 'featured_images')->orWhere('input_name', 'image_external');
        } , 'contracts.translations' , 'city.translations', 'area.translations', 'prices' => function($query) {
            $query->orderBy('lowest_price');
        }, 'allCategories.translations','allCategories'])->whereHas('allCategories', function ($query) {
            $query->where('type', 'opportunity_classifications');
        })->orderBy('views', 'DESC')->limit(3)->get();

        $this->data['recentProjectsOpp'] = Project::select(['be_projects.*',
            DB::raw('
                (
                SELECT trans.title
                FROM be_projects_translations AS trans
                WHERE trans.project_id = be_projects.id
                AND trans.locale = "'. app()->getLocale() .' "
                ) AS new_title
                ')
        ])->translatedIn(app()->getLocale())->with(['translations' ,'allCategories.translations', 'contracts.translations' , 'city.translations', 'area.translations', 'prices' => function($query) {
            $query->orderBy('lowest_price');
        }, 'allCategories.translations','allCategories'])->whereHas('allCategories', function ($query) {
            $query->where('type', 'opportunity_classifications');
        })->orderBy('views', 'DESC')->limit(8)->get();



        $this->data['agent'] = $this->data['model']->contents()->where('type','agents')->first();

        return view('frontend::opportunity.single', $this->data);
    }

}