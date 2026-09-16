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
use Modules\Backend\Entities\ContactUs;

class HomeController extends FrontendController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $this->data['stories'][$this->locale] = Cache::rememberForever('stories_' . $this->locale, function () {
            return Content::translatedIn($this->locale)->with('translations', 'categories.translations')->where('type', 'stories')
                ->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order', 'ASC')->get();
        });

        $this->data['playlists'][$this->locale] = Cache::rememberForever('playlists_' . $this->locale, function () {
            return Content::translatedIn($this->locale)->with('translations')->where(function ($query) {
                $query->where('type', 'playlist_videos');
            })->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order', 'ASC')->limit(4)->get();
        });

        $this->data['recentProjects'][app()->getLocale()] = Cache::rememberForever('recent_projects_' . app()->getLocale(), function () {
            $projectsQuery = Project::translatedIn(app()->getLocale())->whereHas('allCategories', function ($query){
                $query->where('type','property_classifications');
            })->with(['translations', 'propertyFeatures.translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function ($query) {
                $query->orderBy('lowest_price');
            }, 'allCategories' => function ($q) {
                $q->where('type', 'property_classifications')->whereNotNull('parent_id');
            }, 'allCategories.translations']);
            return $projectsQuery->orderBy('created_at', 'DESC')->limit(6)->get();
        });

        //     $this->data['featuredProjects'][app()->getLocale()] = Cache::rememberForever('featured_projects_' . app()->getLocale(), function () {
        //         $projectsQuery = Project::translatedIn(app()->getLocale())->whereHas('allCategories', function ($query){
        //      $query->where('type','property_classifications');
        // })->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function ($query) {
        //             $query->orderBy('lowest_price');
        //         }, 'allCategories' => function ($q) {
        //             $q->where('type', 'property_classifications')->whereNotNull('parent_id');
        //         }, 'allCategories.translations', 'attachments' => function ($query) {
        //             $query->where('input_name', 'featured_images')->orWhere('input_name', 'image_external');
        //         }]);

        //         return $projectsQuery->orderBy('views', 'DESC')->limit(6)->get();
        //     });


        $this->data['featuredProjects'][app()->getLocale()] =  Project::translatedIn(app()->getLocale())->whereHas('allCategories', function ($query){
            $query->where('type','property_classifications');
        })->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function ($query) {
            $query->orderBy('lowest_price');
        }, 'allCategories' => function ($q) {
            $q->where('type', 'property_classifications')->whereNotNull('parent_id');
        }, 'allCategories.translations', 'attachments' => function ($query) {
            $query->where('input_name', 'featured_images')->orWhere('input_name', 'image_external');
        }])->orderBy('views', 'DESC')->limit(6)->get();

        $this->data['sliderImages'] =
            Project::where('is_special', 1)->with('attachments')->whereHas('attachments', function ($query) {
                $query->where('input_name', 'slider_images');
            })->orderBy('created_at', 'DESC')->limit(10)->get();

            $this->data['SliderProject'][app()->getLocale()] = Project::where('is_featured','yes')->translatedIn(app()->getLocale())->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function ($query) {
                $query->orderBy('lowest_price');
            }, 'allCategories' => function ($q) {
                $q->where('type', 'property_classifications')->whereNotNull('parent_id');
            }, 'allCategories.translations', 'attachments' => function ($query) {
                $query->where('input_name', 'featured_images')->orWhere('input_name', 'image_external');
            }])->orderBy('views', 'DESC')->limit(15)->get();
   

        $this->data['testimonials'][$this->locale] = Cache::rememberForever('testimonials_' . $this->locale, function () {
            return Content::translatedIn($this->locale)->with('translations')->where(function ($query) {
                $query->where('type', 'testimonials');
            })->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('type')->orderBy('sort_order', 'ASC')->limit(6)->get();
        });

        $this->data['articles'][$this->locale] = Cache::rememberForever('articles11_' . $this->locale, function () {
            return Content::translatedIn($this->locale)->with('translations', 'categories.translations')->where('type', 'articles')
            // ->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])
            ->orderBy('id', 'DESC')->limit(3)->get();
        });

        // $this->data['c_area'] = Cookie::get('c_area');
        // dd($this->data['c_area']);
        return view('frontend::layouts.index', $this->data);
    }

    public function setCurrency(Request $request)
    {
        $curr = $request->currency;

        Cookie::queue('default-currency', $curr);

        return redirect()->back();
    }

    public function clearCache()
    {
        // Only holders of the aside menu item's ability (tags.requests) may flush the cache (S7).
        \Illuminate\Support\Facades\Gate::authorize('requests', \Modules\Cms\Entities\Tag::class);
        // Artisan::call('cache:clear');
        // Artisan::call('config:clear');
        Cache::flush();
        Cache::forget('featured_projects_ar');
        Cache::forget('featured_projects_en');

        return redirect()->back();
    }
}
