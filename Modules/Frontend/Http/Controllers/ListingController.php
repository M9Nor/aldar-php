<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Frontend\Http\Controllers\FrontendController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Modules\Backend\Entities\Price;
use Modules\Backend\Entities\Project;
use Modules\Cms\Entities\Area;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\Content;
use Symfony\Component\Console\Input\Input;
use Modules\Cms\Entities\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Modules\Cms\Entities\FlagCountry;
use Session;

class ListingController extends FrontendController
{
    public static function pageNotTranslated($trasnlatedContentLocales)
    {
        $all_countries = FlagCountry::where('code','!=','')->where('status','yes')->orderBy('order_by','DESC')->get();
        return view('frontend::pages.page_not_translated.index', compact('trasnlatedContentLocales','all_countries'));
    }

    public function getContentBySlug(Request $request)
    {
        $this->data['model'] = Content::with(['translations','externalAttachments','attachments', 'tags' => function($tag) {
            $tag->with('translations')->translatedIn(app()->getLocale());
        }])->where('slug', $request->slug)->firstOrFail();

        if(!$this->data['model']->hasTranslation(app()->getLocale()))
        {
            $trasnlatedContentLocales = $this->data['model']->translations()->pluck('locale');
            return self::pageNotTranslated($trasnlatedContentLocales);
        }


        switch($this->data['model']->type) {
            case 'filters':
            $parts = parse_url($this->data['model']->link);
            if(isset($parts['query'])){
                parse_str($parts['query'], $query);
            }else{
                $query = [];
            }
            $parameters = collect(explode('/', parse_url($this->data['model']->link, PHP_URL_PATH)));

            $parameters = $parameters->filter(function($value, $key) {
                return !empty($value) && !in_array($value, \LaravelLocalization::getSupportedLanguagesKeys());
            });

            $parameters = $parameters->values()->mapWithKeys(function($value, $key) {
                switch ($key) {
                    case 0:
                    $key = 'property_classification';
                    break;
                    case 1:
                    $key = 'contract';
                    break;
                    case 2:
                    $key = 'city';
                    break;
                    case 3:
                    $key = 'area';
                    break;
                }
                return [$key => $value];
            })->merge(collect($query));

            if($request->has('sort')) $parameters = $parameters->merge(['sort' => $request->sort]);
            $this->data['view_name'] = 'filter';
            return $this->processFilter($request->merge(array_merge($parameters->all(), ['filter_url' => $this->data['model']->link])));
            break;

            case 'services':
            return view('frontend::services.single', $this->data);
            break;

            case 'pages':
            $this->data['services'] = Content::where('type', 'services')->translatedIn(app()->getLocale())->paginate(10);
            if($this->data['model']->slug == 'contact-us')
            {
                return view('frontend::pages.contact_us', $this->data);
            }
            else
            {
                return view('frontend::pages.page', $this->data);
            }
            
            break;
            case 'stories':
            return view('frontend::pages.stories', $this->data);
            break;
        }
    }

    public function prepareFilter(Request $request)
    {
        // dd($request->all());
        $array = [];
        if(!$request->city){
            $array += array('city' => request('city', 'turkey'));
        }else{
            $array += array('city' => request('city', $request->city));
        }

        if(!$request->property_classification){
            $array += array('property_classification' => request('property_classification', 'properties'));
        }elseif($request->property_classification == 'all'){
            $array += array('property_classification' => "all_properties");
        }else{
            $array += array('property_classification' => request('property_classification', $request->property_classification));
        }

        if(!$request->contract){
            $array += array('contract' =>  'all');
        }elseif($request->contract == 'all'){
            $array += array('contract' => "all_contract");
        }else{
            $array += array('contract' => request('contract', $request->contract));
        }
        return redirect(route('ListingController@filter', array_merge($request->except('_token'), $array)));
    }

    public function processFilter(Request $request)
    {   
        //dd(request()->contract);
        $this->data['property_classification']  = $request->property_classification;
        $this->data['contract']                 = $request->contract;
        $this->data['city']                     = $request->city;
        $this->data['area']                     = $request->area;
        $this->data['params']                   = $request->all();
        // dd($this->data['city']);
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

        if(request()->property_classification != 'properties' && request()->property_classification != 'all_properties' ){
        $propertyClassification = Category::where('slug', request()->property_classification)->firstOrFail();
        }
        else{
            $propertyClassification = ''; 
        }
        if(request()->contract != 'all' && request()->contract != 'all_contract'){
            $contract = Category::where('slug', request()->contract)->firstOrFail();
        }else{
           $contract = ''; 
       }

       // if ($city->native_name !='turkey')
       //  $areas = Area::where('city_id', $city->id)->with('translations')->get();

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
    $selected_currency  = Cookie::get('default-currency') ?: "TRY";
    $equivalent         = Content::where('type','currencies')->where('currency_code', $selected_currency)->first()->currency_value;
    $tr_equivalent      = Content::where('type','currencies')->where('currency_code', 'TRY')->first()->currency_value;

    $equivalent         = (float) $equivalent;
    // dd($equivalent);
    $f_min_price        = (float) request()->min_price;
    $f_max_price        = (float) request()->max_price;

    $r_min_price        = (float) $f_min_price / $equivalent;
    $r_max_price        = (float) $f_max_price / $equivalent;
    
    // dd($r_min_price , $r_max_price , $f_min_price , $equivalent);

    $this->data['all_properties'] = Project::whereHas('city', function ($query) use ($city) {
        if($city != '') $query->where('native_name', $city->native_name);
    })
    ->when($area, function ($query) use ($area) {
        $query->where(function($q) use ($area) {
            $q->whereHas('area', function ($qu) use ($area) {
                $qu->where('native_name', $area->native_name);
            });
        });
    })
    ->whereHas('propertyClassifications', function ($query) use ($propertyClassification) {
        if($propertyClassification != '') $query->where('slug', $propertyClassification->slug);
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
    // ->leftJoin('price', 'price.project_id', 'be_projects.id')
    // ->join('price', function($q){
    //     $q->on('price.project_id', 'be_projects.id');
    // })
    ->leftJoin(
        DB::raw('(
            SELECT 
                p.project_id AS project_id,
                MIN(p.lowest_price) AS lowest_price,
                MIN(p.highest_price) AS highest_price,
                MIN(p.full_lowest_price) AS full_lowest_price,
                MIN(p.full_highest_price) AS full_highest_price,
                
                MIN(p.highest_area) AS highest_area,
                MIN(p.is_sold) AS is_sold,
                MIN(p.lowest_area) AS lowest_area,
                MIN(p.room_number) AS room_number
            FROM price AS p
            GROUP BY p.project_id
            ORDER BY p.lowest_price ASC
        ) AS min_max_price'),
        function($join) {
            $join->on('min_max_price.project_id', '=', 'be_projects.id');
        }
    )
    ->select('be_projects.id as id',
        'be_projects.created_at',
        'be_projects.slug as slug',
        'be_projects.image as image',
        'be_projects.city_id',
        'be_projects.country_id',
        'be_projects.area_id',
        'min_max_price.is_sold AS is_sold',
        'min_max_price.lowest_price AS lowest_price',
        'min_max_price.highest_price AS max_price',
        'min_max_price.full_lowest_price AS full_lowest_price',
        'min_max_price.full_highest_price AS full_highest_price',
        'min_max_price.lowest_area AS min_area',
        'min_max_price.highest_area AS max_area',
        'min_max_price.room_number AS room_number',
        // \DB::raw("MIN(price.is_sold) AS is_sold"),
        // \DB::raw("MIN(price.lowest_price) AS min_price"),
        // \DB::raw("MIN(price.highest_price) AS max_price"),
        // \DB::raw("MIN(price.full_lowest_price) AS full_lowest_price"),
        // \DB::raw("MIN(price.full_highest_price) AS full_highest_price"),
        // \DB::raw("MIN(price.lowest_area) AS min_area"),
        // \DB::raw("MIN(price.highest_area) AS max_area"),
        // \DB::raw("MIN(price.room_number) AS room_number")
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
    $r_min_price = (int)$r_min_price;
    $r_max_price = (int)$r_max_price;
    // dd($this->data['all_properties']->where('id',173)->get(),$r_min_price,$r_max_price);
    if(!empty($r_min_price)){
        $this->data['all_properties'] = $this->data['all_properties']
            ->where('min_max_price.full_lowest_price', '>=', $r_min_price);
    }
    if(!empty($r_max_price)){
        $this->data['all_properties'] = $this->data['all_properties']
            ->where('min_max_price.full_highest_price', '<=', $r_max_price);
    }
    
    
    $this->data['all_properties'] = $this->data['all_properties']
    // ->when(request()->min_price, function ($query, $minPrice) {
    //     $query->where('price.lowest_price', '>=', $minPrice);
    // })
    // ->when(request()->max_price, function ($query, $maxPrice) {
    //     $query->where('price.highest_price', '<=', $maxPrice);
    // })
    ->when(request()->min_area, function ($query, $minArea) {
        $query->where('min_max_price.lowest_area', '>=', $minArea);
    })
    ->when(request()->max_area, function ($query, $maxArea) {
        $query->where('min_max_price.highest_area', '<=', $maxArea);
    })
    ->when(request()->rooms,function ($query) {
        // dd($this->data['rooms']);
        $query->whereIn('min_max_price.room_number', $this->data['rooms']);
    })
    ->when(request()->living,function ($query) {
        $query->whereIn('min_max_price.salons_number', $this->data['living']);
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
            $query->orderBy('min_max_price.lowest_price', 'ASC');
            break;
            case 'price_desc':
            $query->orderBy('min_max_price.lowest_price', 'DESC');
            break;
            case 'area_asc':
            $query->orderBy('min_max_price.lowest_area', 'ASC');
            break;
            case 'area_desc':
            $query->orderBy('min_max_price.lowest_area', 'DESC');
            break;
        }
    })
    // ->translatedIn(app()->getLocale())
    ->with([
        'translations',
        'city.translations',
        'area',
        'attachments',
        'prices',
        'categories' => function($query){
            $query->with('translations');
        }
    ])
    ->paginate(6);
    // ->get();

    
    
    // dd($propertyClassification);
    $propertyClassificationTitle    = !empty($propertyClassification) ? $propertyClassification->translateOrFirst(app()->getLocale())->title : '';
    $propertyTypeTitle              = !empty($property_type) ? $property_type->translateOrFirst(app()->getLocale())->title : '';
    $featureTitle                   = !empty($feature) ? $feature->translateOrFirst(app()->getLocale())->title : '';
    if($contract != 'all') {
        $contractTitle                  = !empty($contract) ? $contract->translateOrFirst(app()->getLocale())->title : '';
    }else{
        $contractTitle = '';
    }

    if(empty($city)){
        $cityName = City::where('native_name', $this->data['city'])->withTrashed()->with('translations')->first();
        if(!empty($cityName)){
            $cityName = $cityName->translateOrFirst(app()->getLocale())->name;
        }
        // dd($cityName,$this->data['city']);
    }else{
        $cityName = $city->translateOrFirst(app()->getLocale())->name;
    }
    // $cityName                       = !empty($city) ? $city->translateOrFirst(app()->getLocale())->name : '';
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
        'property_type'     => $propertyClassificationTitle,
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

    // dd($autoGeneratedData , $this->data['texts']);

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
    $this->data['stories'][$this->locale] = Cache::rememberForever('stories_' . $this->locale, function() {
        return Content::translatedIn($this->locale)->with('translations', 'categories.translations')->where('type','stories')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->get();
    });

    $this->data['view_name'] = 'filter';
    // Session::flash('aaaaaa', $request->area);
    Session::put('aaaaaa', $request->area);
    if(!empty($request->area)){
        Cookie::queue('c_area', $request->area);
        // Session::flash('c_area', $request->area);
        // dd($this->data['c_area'],$request->area);
    }
    // dump($this->data['properties']->where('be_projects.id',156));
    // dd($r_min_price , $r_max_price,222);
    // $this->data['all_properties'] = [];
    // $this->data['currencies'] = Content::with('translations')->where('type','currencies')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->get();

    return view('frontend::filter.index', $this->data);
}
public function test(){
    return view('frontend::filter.test', $this->data);
}
public function filter(Request $request)
{
    $url = request()->fullUrl();

    return $this->processFilter($request->merge(['filter_url' => $url]));
}

public function getRegionsByCityId($id)
{
// dd('aaaaaaaaaa');
    $filters = Area::when($id != 'turkey', function($query) use ($id) {
        $query->where('city_id',$id);
    })->with(['translations' => function($query){}])->get();

    return response()->json([
        'success' => true,
        'data' => $filters,
    ]);
}

public function search(Request $request)
{
    $q = request('q', null);

    if (empty($q))
        return redirect()->route('index');

    $types = [
        'filters',
        'playlist_videos',
        'testimonials',
        'articles',
        'achievements',
        'tags',
        'pages',
        'opportunity',
    ];

    $type = in_array(request('type', 'all'), array_merge($types, ['all', 'projects'])) ? request('type', 'all') : 'all';

    if(in_array($type, ['all', 'projects']))
    {
        $this->data['properties'] = Project::where(function($query) use ($q) {
            $query->whereHas('translations', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->orWhere('title', 'like', '%' . $q . '%');
                    $query->orWhere('description', 'like', '%' . $q . '%');
                    $query->orWhere('about', 'like', '%' . $q . '%');
                    $query->orWhere('brief', 'like', '%' . $q . '%');
                });
            })
            ->orWhereHas('tags', function ($query) use ($q) {
                $query->whereHas('translations', function ($query) use ($q) {
                    $query->where('text', 'like', '%' . $q . '%');
                });
            });
        })
        ->whereHas('allCategories', function ($query){
         $query->where('type','property_classifications');
        })
        ->orWhere('code', 'like', '%' . $q . '%')
        ->whereNotNull('slug')
        ->translatedIn(app()->getLocale())
        ->with([
            'translations',
            'city.translations',
            'area.translations',
            'attachments',
            'allCategories',
            'categories' => function($query) {
                $query->with('translations');
            },
            'tags.translations'
        ]);
    }
    $this->data['opportunity'] = Project::where(function($query) use ($q) {
            $query->whereHas('translations', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->orWhere('title', 'like', '%' . $q . '%');
                    $query->orWhere('description', 'like', '%' . $q . '%');
                    $query->orWhere('about', 'like', '%' . $q . '%');
                    $query->orWhere('brief', 'like', '%' . $q . '%');
                });
            })
            ->orWhereHas('tags', function ($query) use ($q) {
                $query->whereHas('translations', function ($query) use ($q) {
                    $query->where('text', 'like', '%' . $q . '%');
                });
            });
        })
        ->orWhere('code', 'like', '%' . $q . '%')
        ->whereNotNull('slug')
        ->translatedIn(app()->getLocale())
        ->with([
            'translations',
            'city.translations',
            'area.translations',
            'attachments',
            'allCategories',
            'categories' => function($query) {
                $query->with('translations');
            },
            'tags.translations'
        ])
        ->whereHas('allCategories', function ($query){
         $query->where('type','opportunity_classifications');
        });
  

    if (!in_array($type, ['tags', 'projects']))
    {
        $this->data['contents_all'] = Content::where(function($query) use ($q) {
            $query->whereHas('translations', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query->orWhere('title', 'like', '%' . $q . '%');
                    $query->orWhere('description', 'like', '%' . $q . '%');
                    $query->orWhere('about', 'like', '%' . $q . '%');
                    $query->orWhere('brief', 'like', '%' . $q . '%');
                });
            })
            ->orWhereHas('tags', function ($query) use ($q) {
                $query->whereHas('translations', function ($query) use ($q) {
                    $query->where('text', 'like', '%' . $q . '%');
                });
            });
        })
        ->where(function ($query) use ($type, $types) {
            if($type == 'all')
            {
                $query->whereIn('type', $types);
            }
            else
            {
                $query->where('type', $type);
            }
        })
        ->translatedIn(app()->getLocale())
        ->with([
            'translations',
            'tags.translations'
        ]);
    }

    if($type == 'tags')
    {
        $this->data['tags'] = Tag::translatedIn(app()->getLocale())
        ->whereHas('translations', function ($query) use($q) {
            $query->where(function ($query) use($q){
                $query->where('text' , 'like' , '%' . $q . '%');
            });
        });
    }

    $this->data['contentCategories'] = collect();

    if ($type == 'all')
    {
        $this->data['properties'] = $this->data['properties']->paginate(10);

        if($this->data['properties']->count() < 10)
        {
            $this->data['contents_all'] = $this->data['contents_all']->where('type', 'articles')->paginate(10 - $this->data['properties']->count(), ['*'], 'page', (int) $this->data['properties']->currentPage() - floor($this->data['properties']->total() / 10));
        }
        else
        {
            $this->data['contents_all'] = $this->data['contents_all']->where('type', 'no-type')->paginate();
        }

        $total = $this->data['properties']->total() + $this->data['contents_all']->total();

        $perPage = 10;

        $items = array_merge($this->data['properties']->items(), $this->data['contents_all']->items());

        $this->data['paginatedData'] = new LengthAwarePaginator($items, $total, $perPage, request('page', 1), ['path' => $request->url()]);

        $this->data['contentCategories'] = Category::with('translations')
        ->get();
    }
    elseif($type == 'projects')
    {
        $this->data['paginatedData'] = $this->data['properties']->paginate(10);
    }
    elseif($type == 'tags')
    {
        $this->data['paginatedData'] = $this->data['tags']->paginate(30);
    }
    elseif($type == 'opportunity')
    {
        $this->data['paginatedData'] = $this->data['opportunity']->paginate(30);
    }
    else
    {
        $this->data['paginatedData'] = $this->data['contents_all']->paginate(10);

        $this->data['contentCategories'] = Category::with(['contents' => function($content) {
            $content->whereIn('id', $this->data['paginatedData']->where('type', 'playlist_videos')->pluck('id')->toArray());
        }])
        ->get();
    }

    $this->data['params'] = [
        'q'     => $q,
        'type'  => $type,
    ];

    return view('frontend::search.index', $this->data);
}

public function articles(Request $request)
{
    $this->data['parents'] = [];
    $this->data['allCategories'] = Category::with('translations')->where('type', 'articles')->get();
    if($request->slug)
    {
        $this->data['category'] = Category::with('contents', 'children')->where('type', 'articles')->where('slug', $request->slug)->first();

        if(!is_null($this->data['category']))
        {
        // get parent to view it in breadcrumb
            $this->data['parents'] = $this->data['category']->AllCategoryParents($this->data['category'], [], $this->data['allCategories']);

        // get category children to view it in sidebar
            $this->data['categoryChildren'] = $this->data['category']->getAllChildren();
            $this->data['allCategories'] = clone $this->data['categoryChildren'];
            $this->data['categoriesIds'] = $this->data['categoryChildren']->push($this->data['category'])->pluck('id');

            $this->data['articles'] = Content::where('type', 'articles')->translatedIn(app()->getLocale())->whereHas('categories', function($q) {
                $q->whereIn('id', $this->data['categoriesIds']);
            })->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->paginate(10);

            return view('frontend::articles.index', $this->data);
        }
        else
        {
            
            $this->data['model'] = Content::with('translations', 'categories')->where('slug', $request->slug)->firstOrFail();

            $this->data['model']->views = $this->data['model']->views + 1;
            $this->data['model']->save();
            
            if(!$this->data['model']->hasTranslation(app()->getLocale()))
            {
                $trasnlatedContentLocales = $this->data['model']->translations()->pluck('locale');
                return self::pageNotTranslated($trasnlatedContentLocales);
            }

            $this->data['relatedArticles'] = Content::where('id','!=',$this->data['model']->id)->translatedIn(app()->getLocale())->with('translations', 'categories')->where('type', 'articles')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->take(3)->get();

            $this->data['allCategories'] = Category::with('translations')->where('type', 'articles')->has('contents')->get();

            $this->data['category'] = $this->data['model']->categories->where('type', 'articles')->first();

            if(!is_null($this->data['category']))
            {
                $this->data['parents'] = $this->data['category']->AllCategoryParents($this->data['category'], [], $this->data['allCategories']);
                $this->data['parents'] = collect($this->data['parents'])->push($this->data['category']);
            }
            
            
            return view('frontend::articles.single', $this->data);
        }
    }

    else
    {
        $this->data['articles'] = Content::
        // select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])
        orderBy('id','DESC')->where('type', 'articles')->translatedIn(app()->getLocale())->paginate(8);

        return view('frontend::articles.index', $this->data);
    }
}
public function achievements(Request $request)
{
    $this->data['parents'] = [];
    $this->data['allCategories'] = Category::with('translations')->where('type', 'achievements')->get();
    if($request->slug)
    {
        $this->data['category'] = Category::with('contents', 'children')->where('type', 'achievements')->where('slug', $request->slug)->first();

        if(!is_null($this->data['category']))
        {
        // get parent to view it in breadcrumb
            $this->data['parents'] = $this->data['category']->AllCategoryParents($this->data['category'], [], $this->data['allCategories']);

        // get category children to view it in sidebar
            $this->data['categoryChildren'] = $this->data['category']->getAllChildren();
            $this->data['allCategories'] = clone $this->data['categoryChildren'];
            $this->data['categoriesIds'] = $this->data['categoryChildren']->push($this->data['category'])->pluck('id');
            $this->data['achievements'] = Content::where('type', 'achievements')->translatedIn(app()->getLocale())->whereHas('categories', function($q) {
                $q->whereIn('id', $this->data['categoriesIds']);
            })->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->paginate(10);

        // dd($this->data['category']->getTranslatedImage('85x85'));
            return view('frontend::achievements.index', $this->data);
        }
        else
        {
            
            $this->data['achievement'] = Content::with('translations', 'categories')->where('slug', $request->slug)->firstOrFail();

            $this->data['achievement']->views = $this->data['achievement']->views + 1;
            $this->data['achievement']->save();
            
            if(!$this->data['achievement']->hasTranslation(app()->getLocale()))
            {
                $trasnlatedContentLocales = $this->data['achievement']->translations()->pluck('locale');
                return self::pageNotTranslated($trasnlatedContentLocales);
            }

            $this->data['relatedAchievements'] = Content::where('id','!=',$this->data['achievement']->id)->translatedIn(app()->getLocale())->with('translations', 'categories')->where('type', 'achievements')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->take(3)->get();

            $this->data['allCategories'] = Category::with('translations')->where('type', 'achievements')->has('contents')->get();

            $this->data['category'] = $this->data['achievement']->categories->where('type', 'achievements')->first();

            if(!is_null($this->data['category']))
            {
                $this->data['parents'] = $this->data['category']->AllCategoryParents($this->data['category'], [], $this->data['allCategories']);
                $this->data['parents'] = collect($this->data['parents'])->push($this->data['category']);
            }
            
            
            return view('frontend::achievements.single', $this->data);
        }
    }

    else
    {
        $this->data['achievements'] = Content::select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->where('type', 'achievements')->translatedIn(app()->getLocale())->paginate(10);
        return view('frontend::achievements.index', $this->data);
    }
}


public function channel(Request $request)
{
    $this->data['allPlaylists'] = Category::with('translations')->where('type', 'playlist_videos')->get();

    if($request->slug)
    {
        $this->data['playlist'] = Category::with('contents')->where('type', 'playlist_videos')->where('slug', $request->slug)->first();

        if(!is_null($this->data['playlist']))
        {
            $this->data['videos'] = Content::where('type', 'playlist_videos')->translatedIn(app()->getLocale())->whereHas('categories', function($q) {
                $q->where('id', $this->data['playlist']->id);
            })->get();

            return view('frontend::channel.playlist-single', $this->data);
        }
        else
        {
            $this->data['video'] = Content::with('translations', 'categories')->where('slug', $request->slug)->firstOrFail();

            $this->data['video']->views = $this->data['video']->views + 1;
            $this->data['video']->save();

            if(!$this->data['video']->hasTranslation(app()->getLocale()))
            {
                $trasnlatedContentLocales = $this->data['video']->translations()->pluck('locale');
                return self::pageNotTranslated($trasnlatedContentLocales);
            }

            $this->data['otherPlaylistVideos'] = Content::translatedIn(app()->getLocale())->with('translations', 'categories')->where('type', 'playlist_videos')->orderBy('created_at', 'desc')->take(3)->get();

            $this->data['allPlaylists'] = Category::with('translations')->where('type', 'playlist_videos')->has('contents')->get();


            $this->data['playlist'] = $this->data['video']->categories->where('type', 'playlist_videos')->first();

            if(!is_null($this->data['playlist']))
            {
                $this->data['parents'] = $this->data['playlist']->AllCategoryParents($this->data['playlist'], [], $this->data['allPlaylists']);
                $this->data['parents'] = collect($this->data['parents'])->push($this->data['playlist']);
            }

            return view('frontend::channel.video-single', $this->data);
        }
    }
    else
    {
        $this->data['playlists'] = Category::where('type', 'playlist_videos')->translatedIn(app()->getLocale())->withCount('contents')->paginate(10);

        return view('frontend::channel.index', $this->data);
    }
}

public function services(Request $request)
{
    $this->data['services'] = Content::where('type', 'services')->translatedIn(app()->getLocale())->paginate(10);

    return view('frontend::services.index', $this->data);
}
public function testimonials(Request $request)
{
    $this->data['testimonials'] = Content::where('type', 'testimonials')->translatedIn(app()->getLocale())->paginate(10);

    return view('frontend::testimonials.index', $this->data);
}

public function faqs(Request $request)
{
    $this->data['faqs'] = Category::with(['translations', 'contents' => function($content) {
        $content->with('translations')->translatedIn(app()->getLocale());
    }])->translatedIn(app()->getLocale())->where('type', 'faqs')->get();

    $this->data['allCategories'] = Category::with('translations')->where('type', 'faqs')->get();
    $this->data['services'] = Content::where('type', 'services')->translatedIn(app()->getLocale())->paginate(10);
    return view('frontend::faqs.index', $this->data);
}
public function getInstallmentsByPayment($id){
// dd($id);
    $filters = Category::where('parent_id',$id)->with('translations')->get();

    return response()->json([
        'success' => true,
        'data' => $filters,
    ]);
}
}
