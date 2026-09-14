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
use Illuminate\View\View;
use Modules\Cms\Entities\Area;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\City;

class FilterComposer
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

        $this->data['cities'][$this->locale] = Cache::rememberForever('cities_' . $this->locale, function() {
            return City::with('translations')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->withCount('projects')->orderBy('sort_order','ASC')->get();
        });
        $types = [
            'contracts',
            'property_classifications',
            'opportunity_classifications',
            'property_status',
            'property_features',
            'payments',
            'Rooms',
            'LivingRooms',
            'bathrooms',
            'facilities'
        ];

        $this->data['filters'] = Cache::remember('filters', 1440, function () use ($types) {
            return Category::select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->whereIn('type',$types)->with('translations')->get();
        });

        $view->with($this->data);
    }
}
