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
use Modules\Cms\Entities\Attachment;
use Modules\Backend\Entities\PropertyForm;
use Modules\Cms\Entities\FlagCountry;
use Modules\Cms\Classes\ResponseHandler;
use Validator;
use Storage;
use DB;
use Modules\Backend\Entities\Price;
use Modules\Cms\Entities\Content;

class PropertyController extends FrontendController
{
    public $attributeNames;

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public static function pageNotTranslated($trasnlatedContentLocales)
    {
        $all_countries = FlagCountry::where('code','!=','')->where('status','yes')->orderBy('order_by','DESC')->get();
        return view('frontend::pages.page_not_translated.index', compact('trasnlatedContentLocales','all_countries'));
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
                    'property_classifications',
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
            ->where('type', 'property_classifications')->whereNotNull('parent_id')
            ->first();

        $this->data['similarProjects'] = CrudModel::translatedIn(app()->getLocale())->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function($query) {
            $query->orderBy('lowest_price');
        }, 'allCategories' => function($q) {
            $q->where('type', 'property_classifications')->whereNotNull('parent_id');
        },'allCategories.translations'])
        ->whereHas('categories', function($q) use ($request) {
            $q->where('type', 'property_classifications')->whereNotNull('parent_id')->where('slug', $request->type);
        })
        ->inRandomOrder()->limit(4)->get();

        $this->data['meta']   = [
            'meta_title'            => !is_null($this->data['model']->translateOrFirst()->tab_title) ? $this->data['model']->translateOrFirst()->tab_title : $this->data['model']->translateOrFirst()->title,
            'meta_description'      => $this->data['model']->translateOrFirst()->seo_description,
            'meta_keywords'         => $this->data['model']->translateOrFirst()->trans_keywords,
            'meta_og_img'           => $this->data['model']->getTranslatedImage('1000x750'),
            'meta_og_img_width'     => '600' ,
            'meta_og_img_heigh'     => '600' ,
            'meta_og_url'           => route('PropertyController@single', ['type' => (!is_null($this->data['category']) ? $this->data['category']->slug : 'unknown'), 'slug' => $this->data['model']->slug]),
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
        
        $this->data['featuredProjects'][app()->getLocale()] = \Cache::rememberForever('featured_projects_' . app()->getLocale(), function() {
            $projectsQuery = Project::translatedIn(app()->getLocale())->whereHas('allCategories', function ($query){
         $query->where('type','property_classifications');
    })->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function($query) {
                $query->orderBy('lowest_price');
            }, 'allCategories' => function($q) {
                $q->where('type', 'property_classifications')->whereNotNull('parent_id');
            },'allCategories.translations', 'attachments' => function($query) {
                $query->where('input_name', 'featured_images')->orWhere('input_name', 'image_external')->orWhere('input_name', 'slider_images');
            }]);

            return $projectsQuery->orderBy('views', 'DESC')->limit(3)->get();
        });

        $this->data['agent'] = $this->data['model']->contents()->where('type','agents')->first();
        // dd($this->data['model']->prices);
        return view('frontend::properties.single', $this->data);
    }

    public function saveProperty (Request $request){
        if(is_null($request->property_images)){
            return response()->json([
                'success'           => false,
                'message'           => trans('frontend::main.add_images')
            ]);
        }elseif(count($request->property_images) < 1){
            return response()->json([
                'success'           => false,
                'message'           => trans('frontend::main.add_images')
            ]);
        }
        
        $validator = \Validator::make($request->all(), [
            'advertisers_name'              => 'required|string|max:100|min:3',
            'advertisers_email'             => 'required|email|max:100|min:6',
            'advertisers_phone'             => 'required|digits_between:1,15|numeric|max:999999999999999|min:1',
            'country_code'                  => 'required|string|max:1000|min:1',
            'property_explanation'          => 'required|string|max:1000|min:3',

            'property_city'                 => 'required|string|max:100|min:1',
            'property_area'                 => 'required|string|max:100|min:1',
            'offers'                        => 'required|string|max:100|min:1',
            'property_classifications'      => 'required|string|max:100|min:1',
            'property_status'               => 'required|string|max:100|min:1',
            'price'                         => 'required|string|max:100|min:1',
            // 'max_price'                     => 'required|string|max:100|min:1',
        ]);
        if ($validator->fails()) {
            $errors = [];
            $messages = $validator->messages()->toArray();
            foreach ($messages as $key => $value) {
                $errors[$key] = $value[0];
            }
            $toReturn['data']['error'] = $errors;
            return response()->json($errors, 200);
        }

        try {
            \DB::transaction(function() use ($request) {
                $phone                              = $request->country_code . $request->advertisers_phone;
                $model                              = new PropertyForm;
                $model->advertisers_name            = $request->advertisers_name;
                $model->advertisers_email           = $request->advertisers_email;
                $model->advertisers_phone           = $phone;
                $model->property_explanation        = $request->property_explanation;
                $model->property_city               = $request->property_city;
                $model->property_area               = $request->property_area;
                $model->offers                      = $request->offers;
                $model->property_classifications    = $request->property_classifications;
                $model->property_status             = $request->property_status;
                $model->min_price                   = $request->price;
                // $model->max_price                   = $request->max_price;
                $model->save();

                if (is_array($request->property_images) && !empty($request->property_images)) {
                    $attachments = Attachment::whereIn('id', $request->property_images)->get();
                    foreach ($attachments as $key => $attachment) {
                        $attachment->type               = 'LINKED';
                        // $attachment->title              = is_array(request('attachment_title', [])) && array_key_exists($attachment->id, request('attachment_title')) ? request('attachment_title')[$attachment->id] : null;
                        // $attachment->description        = is_array(request('attachment_description', [])) && array_key_exists($attachment->id, request('attachment_description')) ? request('attachment_description')[$attachment->id] : null;
                        $attachment->attachable_id      = $model->id;
                        $attachment->attachable_type    = get_class($model);
                        $attachment->save();
                    }
                }
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            // return redirect()->back();
            return response()->json([
                'success'           => false,
                'message'           => trans('frontend::main.some_errors_occurred')
            ]);
        }
        return response()->json([
            'success'   => true,
            'disabled'  => true,
            'message'   => trans('frontend::main.sendded'),
            // 'redirect_url'=> route('PropertyController@confirmation'),
        ]);
    }

    public function submitProperty(Request $request)
    {
        $this->data['cities'][$this->locale]    = City::with('translations')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->get();
        $types = [
            'contracts',
            'property_classifications',
            'property_status',
            // 'property_features',
            // 'payments',
            // 'Rooms',
            // 'LivingRooms',
            // 'bathrooms',
            // 'facilities'
        ];
        $this->data['filters']                  = Category::select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->whereIn('type',$types)->with('translations')->get();
        
        // dd($this->data['filters']);
        return view('frontend::properties.submit', $this->data);
    }
    public function confirmation(Request $request){
        return view('frontend::properties.confirmation', $this->data);
    }

    public function store(Request $request)
    {
        // dd(1);
        $this->attributeNames = [
            'attachment' => __('cms::global.attachment'),
        ];
        // dd($request->validation_rules);
        $rules = [
            'attachment'        => $request->validation_rules ?? 'required',
            'attachable_id'     => 'nullable',
            'attachable_type'   => 'nullable|string|max:191'
        ];

        $validator = Validator::make($request->all(), $rules, [], $this->attributeNames);
        
        if($validator->fails())
        {
            // return new ResponseHandler([
            //     'success'       => false,
            //     'type'          => 'danger',
            //     'title'         => __('cms::messages.upload_error.title'),
            //     'description'   => __('cms::messages.upload_error.description', ['filename' => $request->attachment->getClientOriginalName()]),
            //     'errors'        => $validator->getMessageBag()->toArray()
            // ], 422);
            $string = '';
            foreach($validator->getMessageBag()->toArray() as $er){
                // dd($er);
                if(isset($er[0])){
                    $string = $er[0];
                }
            }
            return response()->json([
                'success'           => false,
                'message'           => $string,
                // 'errors'            => $validator->getMessageBag()->toArray()
            ]);
            
        }

        try {
            DB::transaction(function() use ($request) {
                $subFolder = $request->sub_folder ?? 'general';
                if(!empty($subFolder))
                {
                    $subFolder = !Str::startsWith($subFolder, '/') ? "/{$subFolder}" : $subFolder;
                }
                if($attachmentUid = $request->attachment->storeAs('attachments'.$subFolder, $request->attachment->getClientOriginalName()))
                {
                    $toAttach = [
                        'type'              => 'TEMP',
                        // 'uploaded_by'       => auth()->user()->id,
                        'filename'          => $request->attachment->getClientOriginalName(),
                        'uid'               => $attachmentUid,
                        'size'              => $request->attachment->getSize(),
                        'mime'              => $request->attachment->getMimeType(),
                        'input_name'        => $request->input_name ?? null
                    ];

                    if($request->attachable_id && $request->attachable_id != 'null') $toAttach['attachable_id'] = $request->attachable_id;
                    if($request->attachable_type) $toAttach['attachable_type'] = $request->attachable_type;

                    $this->data['attachment'] = Attachment::create($toAttach);
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'success'           => false,
                'message'           => config('debug.enabled') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.save_error.description')
            ]);
        }

        return response()->json([
            'success'           => true,
            'message'           => __('frontend::main.image_added'),
            'attachment'        => $this->data['attachment']->id,
        ]);
        // return new ResponseHandler([
        //     'success'       => true,
        //     'type'          => 'success',
        //     'title'         => __('cms::messages.upload_success.title'),
        //     'description'   => __('cms::messages.upload_success.description', ['filename' => $request->attachment->getClientOriginalName()]),
        //     'attachment'    => $this->data['attachment']->id
        // ]);
    }

    /**
     * Delete attachment from storage.
     * @param Request $request
     * @return Response
     */
    public function destroy(Request $request)
    {
        $rules = [
            'file_id' => 'nullable|exists:cms_attachments,id',
        ];

        $validator = Validator::make($request->all(), $rules, []);

        if($validator->fails())
        {
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.validation_error.title'),
                'description'   => __('cms::messages.validation_error.description'),
                'errors'        => $validator->getMessageBag()->toArray(),
            ], 422);
        }

        try {
            DB::transaction(function() use ($request) {
                $this->data['attachment'] = Attachment::find($request->file_id);
                if($this->data['attachment']) $this->data['attachment']->delete();
            });

            if($this->data['attachment']) app()->ImageManipulator->deleteImage($this->data['attachment']->uid);
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return new ResponseHandler([
                'success'       => false,
                'type'          => 'danger',
                'title'         => __('cms::messages.update_error.title'),
                'description'   => config('debug.enabled') ? $e->getMessage() . ' [' . $e->getLine() . ']' : __('cms::messages.update_error.description')
            ], 409);
        }

        return new ResponseHandler([
            'success'       => true,
            'type'          => 'success',
            'title'         => __('cms::messages.update_success.title'),
            'description'   => __('cms::messages.update_success.description')
        ]);
    }
}
