<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Cms\Http\Controllers\CmsController;
use Modules\Cms\Classes\ResponseHandler;
use Modules\Cms\Entities\LandingPage as CrudModel;
use Validator;
use Bouncer;
use Auth;
use DB;
use LaravelLocalization;
use Modules\Backend\Entities\Project;
use App\Rules\Slug;
use Modules\Cms\Entities\Content;
use Modules\Cms\Entities\Timeline;

class LandingPageController extends CmsController
{
    public $attributeNames;

    public function __construct()
    {
        $this->attributeNames = [
            'slug'                      => __('cms::landing_page.fields.slug.label'),
            'form'                      => __('cms::landing_page.fields.form.label'),
            'form_direction'            => __('cms::landing_page.fields.form_direction.label'),
            'main_color'                => __('cms::landing_page.fields.main_color.label'),
            'second_color'              => __('cms::landing_page.fields.second_color.label'),
            'subject_text'              => __('cms::landing_page.fields.subject_text.label'),
            'subject_text_background'   => __('cms::landing_page.fields.subject_text_background.label'),
            'projects'                  => __('cms::landing_page.fields.projects.label'),
            'projects_ids.*'            => __('cms::landing_page.fields.projects_ids.label'),
            'projects_ids'              => __('cms::landing_page.fields.projects_ids.label'),
            'blogs'                     => __('cms::landing_page.fields.blogs.label'),
            'blogs_ids.*'               => __('cms::landing_page.fields.blogs_ids.label'),
            'blogs_ids'                 => __('cms::landing_page.fields.blogs_ids.label'),
            'services'                  => __('cms::landing_page.fields.services.label'),
            'services_ids.*'            => __('cms::landing_page.fields.services_ids.label'),
            'services_ids'              => __('cms::landing_page.fields.services_ids.label'),
            'videos'                    => __('cms::landing_page.fields.videos.label'),
            'videos_link'               => __('cms::landing_page.fields.videos_link.label'),
            'footer_whatsapp'           => 'whatsapp',
            'footer_facebook'           => 'facebook',
            'footer_youtube'            => 'youtube',
            'footer_instagram'          => 'instagram',
            // 
            'new_timeline.*.language'   => __('cms::landing_page.timeline.language.label'),
            'new_timeline.*.link'       => __('cms::landing_page.timeline.link.label'),
            'new_timeline.*.title'      => __('cms::landing_page.timeline.title.label'),
            'new_timeline.*.description'=> __('cms::landing_page.timeline.description.label'),
            'new_timeline.*.date'       => __('cms::landing_page.timeline.date.label'),
            'new_timeline.*.langlinkuage' => __('cms::landing_page.timeline.link.label'),
            'new_timeline.*.icon'       => __('cms::landing_page.timeline.icon.label'),
            'new_timeline.*.image'      => __('cms::landing_page.timeline.image.label'),
            // 
            'timeline.*.language'       => __('cms::landing_page.timeline.language.label'),
            'timeline.*.link'           => __('cms::landing_page.timeline.link.label'),
            'timeline.*.title'          => __('cms::landing_page.timeline.title.label'),
            'timeline.*.description'    => __('cms::landing_page.timeline.description.label'),
            'timeline.*.sort_order'    => __('cms::landing_page.timeline.sort_order.label'),
            'timeline.*.date'           => __('cms::landing_page.timeline.date.label'),
            'timeline.*.langlinkuage'   => __('cms::landing_page.timeline.link.label'),
            'timeline.*.icon'           => __('cms::landing_page.timeline.icon.label'),
            'timeline.*.image'          => __('cms::landing_page.timeline.image.label'),
            
        ];
        $translationAttributeNames = [];
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            $names = [
                'meta_img_'.$locale             => __('cms::landing_page.meta_img_'.$locale.''),
                'meta_title_'.$locale           => __('cms::landing_page.meta_title_'.$locale.''),
                'timeline_title_'.$locale       => __('cms::landing_page.timeline_title_'.$locale.''),
                'meta_keywords_'.$locale        => __('cms::landing_page.meta_keywords_'.$locale.''),
                'meta_desc_'.$locale            => __('cms::landing_page.meta_desc_'.$locale.''),
                'header_logo_'.$locale          => __('cms::landing_page.header_logo_'.$locale.''),
                'header_background_'.$locale    => __('cms::landing_page.header_background_'.$locale.''),
                'header_h1_'.$locale            => __('cms::landing_page.header_h1_'.$locale.''),
                'header_h2_'.$locale            => __('cms::landing_page.header_h2_'.$locale.''),
                'header_h1_above_'.$locale      => __('cms::landing_page.header_h1_above_'.$locale.''),
                'header_h2_above_'.$locale      => __('cms::landing_page.header_h2_above_'.$locale.''),
                'header_h1_under_'.$locale      => __('cms::landing_page.header_h1_under_'.$locale.''),
                'header_h2_under_'.$locale      => __('cms::landing_page.header_h2_under_'.$locale.''),
                'subject_text_h2_'.$locale      => __('cms::landing_page.subject_text_h2_'.$locale.''),
                'subject_text_desc_'.$locale    => __('cms::landing_page.subject_text_desc_'.$locale.''),
                'videos_title_'.$locale         => __('cms::landing_page.videos_title_'.$locale.''),
                'subject_text2_h2_'.$locale     => __('cms::landing_page.subject_text2_h2_'.$locale.''),
                'subject_text2_h3_'.$locale     => __('cms::landing_page.subject_text2_h3_'.$locale.''),
                'subject_text2_desc_'.$locale   => __('cms::landing_page.subject_text2_desc_'.$locale.''),
                
                
            ];
            $translationAttributeNames = array_merge($translationAttributeNames,$names);
        }
        $this->attributeNames = array_merge($this->attributeNames,$translationAttributeNames);
        $this->middleware('auth')->except([]);
        parent::__construct();
    }
    public function index(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        return view('cms::admin.landingpages.index', $this->data);
    }
    public function data(Request $request)
    {
        $this->authorize('view', CrudModel::class);
        $list = CrudModel::select(['landing_pages.*',
            DB::raw('
                (
                    SELECT trans.meta_title
                    FROM landing_page_translations AS trans
                    WHERE trans.landing_page_id = landing_pages.id
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
            $val = !is_null( $model->new_title ) ? $model->new_title : $model->translations->first()->name;
            $name = '
                <a target="_blank" href="'.route("LandingPageController@details",["slug" => $model->slug]).'">'.$val.'</a>
            ';
            return $name;
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
                        'url'   => route('LandingPageController@edit', ['model' => $model->id]),
                        'id'    => 'edit_' . $model->id
                    ]);
                }
                if(auth()->user()->can('delete', $model) )
                {
                    $items[] = array_merge($this->actions['delete'], [
                        'url'   => route('LandingPageController@destroy', ['model' => $model->id]),
                        'id'    => 'delete_' . $model->id
                    ]);
                }
            }
            if($model->trashed())
            {
                if(auth()->user()->can('restore', $model))
                {
                    $items[] = array_merge($this->actions['restore'], [
                        'url'   => route('LandingPageController@restore', ['model' => $model->id]),
                        'id'    => 'restore_' . $model->id
                    ]);
                }
                if(auth()->user()->can('forceDelete', $model))
                {
                    $items[] = array_merge($this->actions['force_delete'], [
                        'url'   => route('LandingPageController@destroy', ['model' => $model->id]),
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
        $rawColumns[] = 'translated_name';
        $rawColumns[] = 'area';
        $rawColumns[] = 'actions';
        return $datatables
        ->rawColumns($rawColumns)
        ->make(true);
    }
    public function create(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        $this->data['projects']             = Project::whereHas('allCategories', function ($query){
         $query->where('type','property_classifications');
            })->with(['translations','allCategories']);
        $this->data['blogs']            = Content::with('translations')->where('type','articles');
        $this->data['services']         = Content::with('translations')->where('type','services');
        
        return view('cms::admin.landingpages.create', $this->data);
    }
    public function store(Request $request)
    {
        $this->authorize('create', CrudModel::class);
        $rules = [
            // 'country'               => 'required|digits_between:1,9|numeric|max:9999999999|min:1',
            'form'                      => 'required|string|max:25',
            'form_direction'            => 'nullable|string|max:25',
            'main_color'                => 'nullable|string|max:25',
            'second_color'              => 'nullable|string|max:25',
            'slug'                      => ['required','string',new Slug,'max:191','min:3','unique:landing_pages'],
            'subject_text'              => 'required|string|max:25',
            'subject_text_background'   => 'nullable|string|max:25',
            'projects'                  => 'required|string|max:25',
            'projects_ids'              => 'nullable|array',
            'projects_ids.*'            => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'blogs'                     => 'required|string|max:25',
            'blogs_ids'                 => 'nullable|array',
            'blogs_ids.*'               => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'services'                  => 'required|string|max:25',
            'services_ids'              => 'nullable|array',
            'services_ids.*'            => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'videos'                    => 'required|string|max:25',
            'videos_link'               => 'nullable|string|max:255',
            'footer_whatsapp'           => 'nullable|string|max:255',
            'footer_facebook'           => 'nullable|string|max:255',
            'footer_youtube'            => 'nullable|string|max:255',
            'footer_instagram'          => 'nullable|string|max:255',
            // 
            'meta_img_ar'               => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080',
            'meta_title_ar'             => 'required|string|max:255',
            'meta_keywords_ar'          => 'nullable|string|max:255',
            'meta_desc_ar'              => 'nullable|string|max:255',
            'timeline_title_ar'         => 'nullable|string|max:255',
            'header_logo_ar'            => 'required|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080',
            'header_background_ar'      => 'required|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'header_h1_ar'              => 'nullable|string|max:255',
            'header_h2_ar'              => 'nullable|string|max:255',
            'header_h1_above_ar'        => 'nullable|string|max:255',
            'header_h2_above_ar'        => 'nullable|string|max:255',
            'header_h1_under_ar'        => 'nullable|string|max:255',
            'header_h2_under_ar'        => 'nullable|string|max:255',
            'subject_text_h2_ar'        => 'nullable|string|max:255',
            'subject_text_desc_ar'      => 'nullable|string|max:5000',
            'videos_title_ar'           => 'nullable|string|max:255',
            'subject_text2_h2_ar'       => 'nullable|string|max:255',
            'subject_text2_h3_ar'       => 'nullable|string|max:255',
            'subject_text2_desc_ar'     => 'nullable|string|max:5000',
            
        ];
        if(!empty($request->timeline)){
            $rules['timeline.*.language']   = 'required|string|max:10';
            $rules['timeline.*.title']      = 'required|string|max:255';
            $rules['timeline.*.description']= 'required|string|max:2500';
            $rules['timeline.*.sort_order'] = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['timeline.*.date']       = 'nullable|string|max:45';
            $rules['timeline.*.link']       = 'nullable|string|max:255';
            $rules['timeline.*.icon']       = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=10,min_height=10,max_width=1000,max_height:1000';
            $rules['timeline.*.image']      = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
        }
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!is_null($request->input('meta_title_'.$locale))){
                        $rules['meta_img_'.$locale]             = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
                        $rules['meta_title_'.$locale]           = 'required|string|max:255';
                        $rules['meta_keywords_'.$locale]        = 'nullable|string|max:255';
                        $rules['meta_desc_'.$locale]            = 'nullable|string|max:255';
                        $rules['timeline_title_'.$locale]       = 'nullable|string|max:255';
                        $rules['header_logo_'.$locale]          = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
                        $rules['header_background_'.$locale]    = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                        $rules['header_h1_'.$locale]            = 'nullable|string|max:255';
                        $rules['header_h2_'.$locale]            = 'nullable|string|max:255';
                        $rules['header_h1_above_'.$locale]      = 'nullable|string|max:255';
                        $rules['header_h2_above_'.$locale]      = 'nullable|string|max:255';
                        $rules['header_h1_under_'.$locale]      = 'nullable|string|max:255';
                        $rules['header_h2_under_'.$locale]      = 'nullable|string|max:255';
                        $rules['subject_text_h2_'.$locale]      = 'nullable|string|max:255';
                        $rules['subject_text_desc_'.$locale]    = 'nullable|string|max:5000';
                        $rules['videos_title_'.$locale]         = 'nullable|string|max:255';
                        $rules['subject_text2_h2_'.$locale]     = 'nullable|string|max:255';
                        $rules['subject_text2_h3_'.$locale]     = 'nullable|string|max:255';
                        $rules['subject_text2_desc_'.$locale]   = 'nullable|string|max:5000';
                    }
                }
        }
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
                $this->data['model']                            = new CrudModel;
                $this->data['model']->slug                      = $request->slug;
                $this->data['model']->main_color                = $request->main_color;
                $this->data['model']->second_color              = $request->second_color;
                $this->data['model']->form                      = $request->form;
                $this->data['model']->form_direction            = $request->form_direction;
                $this->data['model']->subject_text              = $request->subject_text;
                $this->data['model']->subject_text_background   = $request->subject_text_background;
                $this->data['model']->projects                  = $request->projects;
                if(!empty($request->projects_ids)){
                    $this->data['model']->projects_ids                        = implode(",", $request->projects_ids);
                }
                $this->data['model']->blogs                     = $request->blogs;
                if(!empty($request->blogs_ids)){
                    $this->data['model']->blogs_ids                           = implode(",", $request->blogs_ids);
                }
                $this->data['model']->services                  = $request->services;
                if(!empty($request->services_ids)){
                    $this->data['model']->services_ids                        = implode(",", $request->services_ids);
                }
                $this->data['model']->videos                    = $request->videos;
                $this->data['model']->videos_link               = $request->videos_link;
                $this->data['model']->subject_text2             = $request->subject_text2;
                $this->data['model']->subject_text2_background  = $request->subject_text2_background;

                $this->data['model']->footer_whatsapp           = $request->footer_whatsapp;
                $this->data['model']->footer_facebook           = $request->footer_facebook;
                $this->data['model']->footer_youtube            = $request->footer_youtube;
                $this->data['model']->footer_instagram          = $request->footer_instagram;
                
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('meta_title_'.$locale))){
                        // meta
                        $this->data['model']->{'meta_title:'.$locale}           = $request->input('meta_title_'.$locale);
                        $this->data['model']->{'meta_keywords:'.$locale}        = $request->input('meta_keywords_'.$locale);
                        $this->data['model']->{'meta_desc:'.$locale}            = $request->input('meta_desc_'.$locale);
                        $this->data['model']->{'timeline_title:'.$locale}       = $request->input('timeline_title_'.$locale);
                        ${'metaImagePath'.$locale} = null;
                        if($request->hasFile('meta_img_'.$locale)){
                            ${'metaImagePath'.$locale}                              = $request->file('meta_img_'.$locale)->store('landing_pages');
                            $this->data['model']->{'meta_img:'.$locale}             = ${'metaImagePath'.$locale};
                        }
                        // header
                        ${'headerLogoPath'.$locale} = null;
                        if($request->hasFile('header_logo_'.$locale)){
                            ${'headerLogoPath'.$locale}                             = $request->file('header_logo_'.$locale)->store('landing_pages');
                            $this->data['model']->{'header_logo:'.$locale}          = ${'headerLogoPath'.$locale};
                        }
                        ${'headerBackgroundPath'.$locale} = null;
                        if($request->hasFile('header_background_'.$locale)){
                            ${'headerBackgroundPath'.$locale}                       = $request->file('header_background_'.$locale)->store('landing_pages');
                            $this->data['model']->{'header_background:'.$locale}    = ${'headerBackgroundPath'.$locale};
                        }
                        $this->data['model']->{'header_h1:'.$locale}            = $request->input('header_h1_'.$locale);
                        $this->data['model']->{'header_h2:'.$locale}            = $request->input('header_h2_'.$locale);
                        $this->data['model']->{'header_h1_above:'.$locale}      = $request->input('header_h1_above_'.$locale);
                        $this->data['model']->{'header_h2_above:'.$locale}      = $request->input('header_h2_above_'.$locale);
                        $this->data['model']->{'header_h1_under:'.$locale}      = $request->input('header_h1_under_'.$locale);
                        $this->data['model']->{'header_h2_under:'.$locale}      = $request->input('header_h2_under_'.$locale);
                        $this->data['model']->{'subject_text_h2:'.$locale}      = $request->input('subject_text_h2_'.$locale);
                        $this->data['model']->{'subject_text_desc:'.$locale}    = $request->input('subject_text_desc_'.$locale);
                        $this->data['model']->{'videos_title:'.$locale}         = $request->input('videos_title_'.$locale);
                        $this->data['model']->{'subject_text2_h2:'.$locale}     = $request->input('subject_text2_h2_'.$locale);
                        $this->data['model']->{'subject_text2_h3:'.$locale}     = $request->input('subject_text2_h3_'.$locale);
                        $this->data['model']->{'subject_text2_desc:'.$locale}   = $request->input('subject_text2_desc_'.$locale);
                    }
                }
                $this->data['model']->save();

                if(!empty($request->timeline)){
                    foreach($request->timeline as $item){
                        $timeline               = new Timeline;
                        $timeline->landing_id   = $this->data['model']->id;
                        $timeline->language     = $item['language'];
                        $timeline->title        = $item['title'];
                        $timeline->description  = $item['description'];
                        $timeline->date         = $item['date'];
                        $timeline->link         = $item['link'];
                        $timeline->sort_order   = $item['sort_order'];
                        $iconPath               = $item['icon']->store('timeline');
                        $timeline->icon         = $iconPath;
                        $imagePath              = $item['image']->store('timeline');
                        $timeline->image        = $imagePath;
                        $timeline->save();
                    }
                }
                
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('metaImagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'metaImagePath'.$locale});
                if(array_key_exists('headerLogoPath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'headerLogoPath'.$locale});
                if(array_key_exists('headerLogoPath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'headerLogoPath'.$locale});
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
        $this->data['model']                = CrudModel::withTrashed()->with('timeline')->findOrFail($request->model);
        $this->authorize('update', $this->data['model']);
        $this->data['projects']             = Project::whereHas('allCategories', function ($query){
         $query->where('type','property_classifications');
    })->with(['translations','allCategories']);
        $this->data['selected_projects']    = [];
        if(!empty($this->data['model']->projects_ids)){
            $this->data['selected_projects']    = clone $this->data['projects'];
            $this->data['selected_projects']    = $this->data['selected_projects']->whereIn('id',explode(',', $this->data['model']->projects_ids))->get()->pluck('id')->toArray();
        }

        $this->data['selected_blogs']    = [];
        $this->data['blogs']                = Content::with('translations')->where('type','articles');
        if(!empty($this->data['model']->blogs_ids)){
            $this->data['selected_blogs']    = clone $this->data['blogs'];
            $this->data['selected_blogs']    = $this->data['selected_blogs']->whereIn('id',explode(',', $this->data['model']->blogs_ids))->get()->pluck('id')->toArray();
        }
        
        $this->data['selected_services']    = [];
        $this->data['services']             = Content::with('translations')->where('type','services');
        if(!empty($this->data['model']->services_ids)){
            $this->data['selected_services']    = clone $this->data['services'];
            $this->data['selected_services']    = $this->data['selected_services']->whereIn('id',explode(',', $this->data['model']->services_ids))->get()->pluck('id')->toArray();
        }
        
        // dd($this->data['selected_projects']);
        return view('cms::admin.landingpages.update', $this->data);
    }
    public function update(Request $request)
    {
        $this->data['model'] = CrudModel::findOrFail("{$request->model}");
        $this->authorize('update', $this->data['model']);
        $rules = [
            // 'country'               => 'required|digits_between:1,9|numeric|max:9999999999|min:1',
            'form'                      => 'required|string|max:25',
            'form_direction'            => 'nullable|string|max:25',
            'main_color'                => 'nullable|string|max:25',
            'second_color'              => 'nullable|string|max:25',
            'slug'                      => ['required','string',new Slug,'max:191','min:3','unique:landing_pages,slug,'.$request->model.''],
            'subject_text'              => 'required|string|max:25',
            'subject_text_background'   => 'nullable|string|max:25',
            'projects'                  => 'required|string|max:25',
            'projects_ids'              => 'nullable|array',
            'projects_ids.*'            => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'blogs'                     => 'required|string|max:25',
            'blogs_ids'                 => 'nullable|array',
            'blogs_ids.*'               => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'services'                  => 'required|string|max:25',
            'services_ids'              => 'nullable|array',
            'services_ids.*'            => 'required|digits_between:1,9|numeric|max:999999999|min:1',
            'videos'                    => 'required|string|max:25',
            'videos_link'               => 'nullable|string|max:255',
            'footer_whatsapp'           => 'nullable|string|max:255',
            'footer_facebook'           => 'nullable|string|max:255',
            'footer_youtube'            => 'nullable|string|max:255',
            'footer_instagram'          => 'nullable|string|max:255',
            // 
            'meta_img_ar'               => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080',
            'meta_title_ar'             => 'required|string|max:255',
            'meta_keywords_ar'          => 'nullable|string|max:255',
            'meta_desc_ar'              => 'nullable|string|max:255',
            'timeline_title_ar'         => 'nullable|string|max:255',
            'header_logo_ar'            => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080',
            'header_background_ar'      => 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080',
            'header_h1_ar'              => 'nullable|string|max:255',
            'header_h2_ar'              => 'nullable|string|max:255',
            'header_h1_above_ar'        => 'nullable|string|max:255',
            'header_h2_above_ar'        => 'nullable|string|max:255',
            'header_h1_under_ar'        => 'nullable|string|max:255',
            'header_h2_under_ar'        => 'nullable|string|max:255',
            'subject_text_h2_ar'        => 'nullable|string|max:255',
            'subject_text_desc_ar'      => 'nullable|string|max:5000',
            'videos_title_ar'           => 'nullable|string|max:255',
            'subject_text2_h2_ar'       => 'nullable|string|max:255',
            'subject_text2_h3_ar'       => 'nullable|string|max:255',
            'subject_text2_desc_ar'     => 'nullable|string|max:5000',
            
        ];
        if(!empty($request->new_timeline)){
            $rules['new_timeline.*.language']   = 'required|string|max:10';
            $rules['new_timeline.*.title']      = 'required|string|max:255';
            $rules['new_timeline.*.description']= 'required|string|max:2500';
            $rules['new_timeline.*.sort_order'] = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['new_timeline.*.date']       = 'nullable|string|max:45';
            $rules['new_timeline.*.link']       = 'nullable|string|max:255';
            $rules['new_timeline.*.icon']       = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=10,min_height=10,max_width=1000,max_height:1000';
            $rules['new_timeline.*.image']      = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
        }
        if(!empty($request->timeline)){
            $rules['timeline.*.language']       = 'required|string|max:10';
            $rules['timeline.*.title']          = 'required|string|max:255';
            $rules['timeline.*.description']    = 'required|string|max:2500';
            $rules['timeline.*.sort_order']     = 'required|digits_between:1,9|numeric|max:999999999|min:1';
            $rules['timeline.*.date']           = 'required|string|max:45';
            $rules['timeline.*.link']           = 'required|string|max:255';
            $rules['timeline.*.icon']           = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=10,min_height=10,max_width=1000,max_height:1000';
            $rules['timeline.*.image']          = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
        }
        foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
            if($locale != 'ar'){
                if(!is_null($request->input('meta_title_'.$locale))){
                        $rules['meta_img_'.$locale]             = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
                        $rules['meta_title_'.$locale]           = 'required|string|max:255';
                        $rules['meta_keywords_'.$locale]        = 'nullable|string|max:255';
                        $rules['meta_desc_'.$locale]            = 'nullable|string|max:255';
                        $rules['timeline_title_'.$locale]       = 'nullable|string|max:255';
                        $rules['header_logo_'.$locale]          = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=50,min_height=50,max_width=1920,max_height:1080';
                        $rules['header_background_'.$locale]    = 'nullable|image|mimes:jpeg,png,jpg|max:1024|dimensions:min_width=100,min_height=100,max_width=1920,max_height:1080';
                        $rules['header_h1_'.$locale]            = 'nullable|string|max:255';
                        $rules['header_h2_'.$locale]            = 'nullable|string|max:255';
                        $rules['header_h1_above_'.$locale]      = 'nullable|string|max:255';
                        $rules['header_h2_above_'.$locale]      = 'nullable|string|max:255';
                        $rules['header_h1_under_'.$locale]      = 'nullable|string|max:255';
                        $rules['header_h2_under_'.$locale]      = 'nullable|string|max:255';
                        $rules['subject_text_h2_'.$locale]      = 'nullable|string|max:255';
                        $rules['subject_text_desc_'.$locale]    = 'nullable|string|max:5000';
                        $rules['videos_title_'.$locale]         = 'nullable|string|max:255';
                        $rules['subject_text2_h2_'.$locale]     = 'nullable|string|max:255';
                        $rules['subject_text2_h3_'.$locale]     = 'nullable|string|max:255';
                        $rules['subject_text2_desc_'.$locale]   = 'nullable|string|max:5000';
                    }
                }
        }
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
                $this->data['model']->slug                      = $request->slug;
                $this->data['model']->form                      = $request->form;
                $this->data['model']->form_direction            = $request->form_direction;
                $this->data['model']->main_color                = $request->main_color;
                $this->data['model']->second_color              = $request->second_color;
                $this->data['model']->subject_text              = $request->subject_text;
                $this->data['model']->subject_text_background   = $request->subject_text_background;
                $this->data['model']->projects                  = $request->projects;
                if(!empty($request->projects_ids)){
                    $this->data['model']->projects_ids                        = implode(",", $request->projects_ids);
                }
                $this->data['model']->blogs                     = $request->blogs;
                if(!empty($request->blogs_ids)){
                    $this->data['model']->blogs_ids                           = implode(",", $request->blogs_ids);
                }
                $this->data['model']->services                  = $request->services;
                if(!empty($request->services_ids)){
                    $this->data['model']->services_ids                        = implode(",", $request->services_ids);
                }
                $this->data['model']->videos                    = $request->videos;
                $this->data['model']->videos_link               = $request->videos_link;
                $this->data['model']->subject_text2             = $request->subject_text2;
                $this->data['model']->subject_text2_background  = $request->subject_text2_background;

                $this->data['model']->footer_whatsapp           = $request->footer_whatsapp;
                $this->data['model']->footer_facebook           = $request->footer_facebook;
                $this->data['model']->footer_youtube            = $request->footer_youtube;
                $this->data['model']->footer_instagram          = $request->footer_instagram;
                
                foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                    if(!is_null($request->input('meta_title_'.$locale))){
                        $this->data['model']->{'meta_title:'.$locale}           = $request->input('meta_title_'.$locale);
                        $this->data['model']->{'meta_keywords:'.$locale}        = $request->input('meta_keywords_'.$locale);
                        $this->data['model']->{'meta_desc:'.$locale}            = $request->input('meta_desc_'.$locale);
                        $this->data['model']->{'timeline_title:'.$locale}       = $request->input('timeline_title_'.$locale);
                        ${'metaImagePath'.$locale} = null;
                        if($request->hasFile('meta_img_'.$locale)){
                            ${'metaImagePath'.$locale}                              = $request->file('meta_img_'.$locale)->store('landing_pages');
                            $this->data['model']->{'meta_img:'.$locale}             = ${'metaImagePath'.$locale};
                        }
                        ${'headerLogoPath'.$locale} = null;
                        if($request->hasFile('header_logo_'.$locale)){
                            ${'headerLogoPath'.$locale}                             = $request->file('header_logo_'.$locale)->store('landing_pages');
                            $this->data['model']->{'header_logo:'.$locale}          = ${'headerLogoPath'.$locale};
                        }
                        ${'headerBackgroundPath'.$locale} = null;
                        if($request->hasFile('header_background_'.$locale)){
                            ${'headerBackgroundPath'.$locale}                       = $request->file('header_background_'.$locale)->store('landing_pages');
                            $this->data['model']->{'header_background:'.$locale}    = ${'headerBackgroundPath'.$locale};
                        }
                        $this->data['model']->{'header_h1:'.$locale}            = $request->input('header_h1_'.$locale);
                        $this->data['model']->{'header_h2:'.$locale}            = $request->input('header_h2_'.$locale);
                        $this->data['model']->{'header_h1_above:'.$locale}      = $request->input('header_h1_above_'.$locale);
                        $this->data['model']->{'header_h2_above:'.$locale}      = $request->input('header_h2_above_'.$locale);
                        $this->data['model']->{'header_h1_under:'.$locale}      = $request->input('header_h1_under_'.$locale);
                        $this->data['model']->{'header_h2_under:'.$locale}      = $request->input('header_h2_under_'.$locale);
                        $this->data['model']->{'subject_text_h2:'.$locale}      = $request->input('subject_text_h2_'.$locale);
                        $this->data['model']->{'subject_text_desc:'.$locale}    = $request->input('subject_text_desc_'.$locale);
                        $this->data['model']->{'videos_title:'.$locale}         = $request->input('videos_title_'.$locale);
                        $this->data['model']->{'subject_text2_h2:'.$locale}     = $request->input('subject_text2_h2_'.$locale);
                        $this->data['model']->{'subject_text2_h3:'.$locale}     = $request->input('subject_text2_h3_'.$locale);
                        $this->data['model']->{'subject_text2_desc:'.$locale}   = $request->input('subject_text2_desc_'.$locale);
                    }
                }
                $this->data['model']->save();

                // dd($request->new_timeline);
                if(!empty($request->timeline)){
                    foreach($request->timeline as $key => $item){
                        $timeline               = Timeline::find($key);
                        if(!empty($timeline)){
                            $timeline->landing_id   = $this->data['model']->id;
                            $timeline->language     = $item['language'];
                            $timeline->title        = $item['title'];
                            $timeline->description  = $item['description'];
                            $timeline->date         = $item['date'];
                            $timeline->link         = $item['link'];
                            $timeline->sort_order   = $item['sort_order'];
                            if(!empty($item['icon'])){
                                $iconPath               = $item['icon']->store('timeline');
                                $timeline->icon         = $iconPath;
                            }
                            if(!empty($item['image'])){
                                $imagePath              = $item['image']->store('timeline');
                                $timeline->image        = $imagePath;
                            }
                            $timeline->save();
                        }
                    }
                }
                if(!empty($request->new_timeline)){
                    foreach($request->new_timeline as $item){
                        $timeline               = new Timeline;
                        $timeline->landing_id   = $this->data['model']->id;
                        $timeline->language     = $item['language'];
                        $timeline->title        = $item['title'];
                        $timeline->description  = $item['description'];
                        $timeline->date         = $item['date'];
                        $timeline->link         = $item['link'];
                        $timeline->sort_order   = $item['sort_order'];
                        if(!empty($item['icon'])){
                            $iconPath               = $item['icon']->store('timeline');
                            $timeline->icon         = $iconPath;
                        }
                        if(!empty($item['image'])){
                            $imagePath              = $item['image']->store('timeline');
                            $timeline->image        = $imagePath;
                        }
                        $timeline->save();
                    }
                }
            });
        } catch (\Exception $e) {
            foreach(LaravelLocalization::getSupportedLocales() as $locale => $lang){
                if(array_key_exists('metaImagePath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'metaImagePath'.$locale});
                if(array_key_exists('headerLogoPath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'headerLogoPath'.$locale});
                if(array_key_exists('headerBackgroundPath'.$locale, $this->data)) app()->ImageManipulator->deleteImage(${'headerBackgroundPath'.$locale});
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
                if($this->data['model']->meta_img) app()->ImageManipulator->deleteImage($this->data['model']->meta_img);
                if($this->data['model']->header_logo) app()->ImageManipulator->deleteImage($this->data['model']->header_logo);
                if($this->data['model']->header_background) app()->ImageManipulator->deleteImage($this->data['model']->header_background);
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
                        if($model->meta_img) app()->ImageManipulator->deleteImage($model->meta_img);
                        if($model->header_logo) app()->ImageManipulator->deleteImage($model->header_logo);
                        if($model->header_background) app()->ImageManipulator->deleteImage($model->header_background);
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
    public function deleteTimeline(Request $request)
    {
        $this->data['model'] = Timeline::findOrFail($request->timeline_id);
        // The landing page edit page's ability (S21).
        $this->authorize('update', CrudModel::withTrashed()->findOrFail($this->data['model']->landing_id));
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
