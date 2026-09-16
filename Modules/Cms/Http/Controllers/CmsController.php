<?php

namespace Modules\Cms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Cms\Entities\Country;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\Area;
use LaravelLocalization;
use Modules\Cms\Entities\Content;

class CmsController extends Controller
{
    public function getCountries(Request $request){

        $term = trim((string) $request->search);
        $page = $request->page;
        if(!$page){
            $page = 1;
        }
        $list = Country::where(function($q) use ($term) {
            $q->whereTranslationLike('name',"%{$term}%");
        });
        $list = $list->paginate($request->items_per_page);
        $result['results'] = [];
        foreach($list as $key => $item){
            $result['results'][$key] = $item->formAjaxArray(true);
        }
        $last_page = $list->lastPage();
        $result['pagination']['more'] = $page >= $last_page ? false : true;
        return json_encode($result);
    }

    public function getCities(Request $request){
        $term = trim((string) $request->search);
        $page = $request->page;
        if(!$page){
            $page = 1;
        }
        $list = City::where(function($q) use ($term) {
            $q->whereTranslationLike('name',"%{$term}%");
        });
        $list->when($request->additional_params, function($query, $additional_params) {
            if(array_key_exists('country_id', $additional_params) && !is_null($additional_params['country_id'])){
                $query->where('country_id',$additional_params['country_id']);
            }
        });
        $list = $list->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->paginate($request->items_per_page);
        $result['results'] = [];
        foreach($list as $key => $item){
            $result['results'][$key] = $item->formAjaxArray(true);
        }
        $last_page = $list->lastPage();
        $result['pagination']['more'] = $page >= $last_page ? false : true;
        return json_encode($result);
    }

    public function getAreas(Request $request){

        $term = trim((string) $request->search);
        $page = $request->page;
        if(!$page){
            $page = 1;
        }
        $list = Area::where(function($q) use ($term) {
            $q->whereTranslationLike('name',"%{$term}%");
        });
        $list->when($request->additional_params, function($query, $additional_params) {
            if(array_key_exists('city_id', $additional_params) && !is_null($additional_params['city_id'])){
                $query->where('city_id',$additional_params['city_id']);
            }
            if(array_key_exists('area_id', $additional_params) && !is_null($additional_params['area_id'])){
                $query->where('id','!=',$additional_params['area_id']);
            }
        });
        $list = $list->paginate($request->items_per_page);
        $result['results'] = [];
        foreach($list as $key => $item){
            $result['results'][$key] = $item->formAjaxArray(true);
        }
        $last_page = $list->lastPage();
        $result['pagination']['more'] = $page >= $last_page ? false : true;
        return json_encode($result);
        
    }

    public function getContentsSelect2(Request $request){
        $term = trim((string) $request->search);
        $page = $request->page;
        if(!$page){
            $page = 1;
        }
        $list = Content::select('cms_contents.*');
        // dd($request->type);
        if($request->type != 'all'){
            $list = $list->where('type',$request->type);
        }
        $list = $list->when($request->model, function($query, $model) {
                $query->where('id', '!=', $model);
            })->where(function($q) use ($term) {
                $q->whereTranslationLike('title', "%{$term}%");
            })->orWhere('id',$term);

        $list = $list->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->paginate($request->items_per_page);
        
        $result['results'] = [];
        foreach($list as $key => $item){
            $result['results'][$key] = $item->formAjaxArray(true);
        }
        $last_page = $list->lastPage();
        $result['pagination']['more'] = $page >= $last_page ? false : true;
        return json_encode($result);
    }
}
