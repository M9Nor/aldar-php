<?php
/**
 * Created by PhpStorm.
 * User: Noor
 * Date: 20-Jun-20
 * Time: 12:33 PM
 */
namespace Modules\Frontend\Http\ViewComposers;

use function foo\func;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\Content;

class MenuComposer
{
    public $locale = null;
    public $data = [];

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $this->locale = app()->getLocale();

        $this->data['currencies'] = Cache::rememberForever('currencies', function () {
            return Content::with('translations')->where('type','currencies')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->get();
        });

        $selectedCurrency = Cookie::get('default-currency') ?: "TRY";
        $this->data['selectedCurrency'] = $this->data['currencies']->where('currency_code', $selectedCurrency)->first();

        $this->data['headerMenuItems'] =  Category::
            whereIn('slug',['buy-properties','buy-online','investment-opportunities'])
            ->where('type', 'filters')
            ->whereNotNull('parent_id')
            ->whereHas('parent', function($query) {
                $query->where('slug', 'header-categories');
            })
            ->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])
            ->orderBy('sort_order','ASC')
            ->with(['translations', 'contents' => function($query) {
                $query->with('translations')->where('type', 'filters')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC');
            }])
            ->get();

                $this->data['headerMenuItemsOpportunity'] =  Category::
            where('type','opportunity_classifications')
            ->whereNotNull('parent_id')
            ->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])
            ->orderBy('sort_order','ASC')
            ->with(['translations', 'contents' => function($query) {
                $query->with('translations')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC');
            }])
            ->get();
    

        $this->data['footerMenuItems'] = Cache::rememberForever('footer_menu_items', function () {
            return Category::where('type', 'filters')
            ->whereNotNull('parent_id')
            ->whereHas('parent', function($query) {
                $query->where('slug', 'footer-categories');
            })
            ->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])
            ->orderBy('sort_order','ASC')
            ->with(['translations', 'contents' => function($query) {
                $query->with('translations')->where('type', 'filters')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC');
            }])
            ->get();
        });

        $this->data['services'][$this->locale] = Cache::rememberForever('services_' . $this->locale, function() {
            return Content::translatedIn($this->locale)->with('translations')->where('type','services')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->get();
        });

        $this->data['mainPages'] = Cache::rememberForever('main_pages', function () {
            return Content::where('type','pages')->with('translations')->get();
        });
        
        $view->with($this->data);
    }
}
