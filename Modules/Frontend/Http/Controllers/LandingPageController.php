<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Frontend\Http\Controllers\FrontendController;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Modules\Cms\Entities\Content;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\Config;
use Modules\Backend\Entities\Project;
use Modules\Cms\Entities\LandingPage;

class LandingPageController extends FrontendController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function details($slug)
    {
        $this->data['model']    = LandingPage::with('translations','timeline')->whereHas('translations',function($q){
            $q->where('locale',app()->getLocale());
        })->where('slug',$slug)->firstOrFail();
        $projects_ids           = [];
        if(!empty($this->data['model']->projects_ids)){
            $projects_ids                   = explode(',', $this->data['model']->projects_ids);
            $this->data['projects']         = Project::whereIn('id',$projects_ids)->with([
                'translations',
                'attachments' => function($qu) {
                    $qu->orderBy('input_name');
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
            ])->get();
        }
        $blogs_ids              = [];
        if(!empty($this->data['model']->blogs_ids)){
            $blogs_ids                  = explode(',', $this->data['model']->blogs_ids);
            $this->data['blogs']        = Content::where('type', 'articles')->whereIn('id',$blogs_ids)->translatedIn(app()->getLocale())->limit(3)->get();
        }
        if(!empty($this->data['model']->services_ids)){
            $services_ids                  = explode(',', $this->data['model']->services_ids);
            $this->data['services']        = Content::where('type', 'services')->whereIn('id',$services_ids)->translatedIn(app()->getLocale())->limit(6)->get();
        }
        $this->data['main_color']       = $this->data['model']->main_color;
        $this->data['second_color']     = $this->data['model']->second_color;
        return view('frontend::landingpage.layout', $this->data);
    }
}
