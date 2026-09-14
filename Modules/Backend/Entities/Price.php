<?php

namespace Modules\Backend\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\Content;
use Modules\Backend\Entities\Project;

class Price extends Model
{
    protected $table        = 'price';
    
    protected $fillable = [
        'project_id',
        'balance_id',
        'highest_price',
        'lowest_price',
        'highest_area',
        'lowest_area',
        'bathes_number',
        'salons_number',
        'room_number',
        'discount',
        'is_sold',
        'balance',
        // 'full_highest_price',
        // 'full_lowest_price',
    ];
    // public function category(){
    //     return $this->hasOne(Category::class,'id','balance_id');
    // }
    public function content(){
        return $this->hasOne(Content::class,'id','balance_id');
    }
    
    public function category(){
        return $this->hasOne(Category::class,'id','cat_id');
    }
    public function project(){
        return $this->hasOne(Project::class,'id','project_id');
    }
    public function priceWithCurrency(){

    }


    public function getHighestPriceAttribute($value){

        $price = (float) $value;

        Cache::forget('currencies');

        $desiredCurrencyCode = Cookie::get('default-currency') ?: 'TRY';

        $currencies = Cache::remember('currencies', 720, function()
        {
            return Content::where('type','currencies')->get();
        });

        $desiredCurrency = $currencies->where('currency_code', $desiredCurrencyCode)->first();

        if(!is_null($desiredCurrency))
        {
            $currencyValue = (float) $desiredCurrency->currency_value;
            $currencySymbol = $desiredCurrency->currency_symbol;
        }

        $currentCurrency = $this->content;

        if(!is_null($currentCurrency))
        {
            if($currentCurrency->currency_code == $desiredCurrencyCode)
            {
                if(empty($desiredCurrency))
                    return '₺ ' . number_format(round($price , 2));

                return ' ' . number_format(round($price, 2)) . ' ' . $currencySymbol;
            }
            else if(($currentCurrency->currency_code != $desiredCurrencyCode) && $currentCurrency->currency_code != 'TRY')
            {
                // Convert price to TRY currency. This is because we have the conversion factor from other currencies to TRY.
                $price = $price / (float) $currentCurrency->currency_value;
            }
        }

        if(empty($desiredCurrency))
            return '₺ ' . number_format(round($price , 2));

        return ' ' . number_format(round($price * $currencyValue, 2)) . ' ' . $currencySymbol;


    }

    public function getLowestPriceAttribute($value){

        $price = (float) $value;

        Cache::forget('currencies');

        $desiredCurrencyCode = Cookie::get('default-currency') ?: 'TRY';

        $currencies = Cache::remember('currencies', 720, function()
        {
            return Content::where('type','currencies')->get();
        });

        $desiredCurrency = $currencies->where('currency_code', $desiredCurrencyCode)->first();

        if(!is_null($desiredCurrency))
        {
            $currencyValue = (float) $desiredCurrency->currency_value;
            $currencySymbol = $desiredCurrency->currency_symbol;
        }

        $currentCurrency = $this->content;

        if(!is_null($currentCurrency))
        {
            if($currentCurrency->currency_code == $desiredCurrencyCode)
            {
                if(empty($desiredCurrency))
                    return '₺ ' . number_format(round($price , 2));

                return ' ' . number_format(round($price, 2)) . ' ' . $currencySymbol;
            }
            else if(($currentCurrency->currency_code != $desiredCurrencyCode) && $currentCurrency->currency_code != 'TRY')
            {
                // Convert price to TRY currency. This is because we have the conversion factor from other currencies to TRY.
                $price = $price / (float) $currentCurrency->currency_value;
            }
        }

        if(empty($desiredCurrency))
            return '₺ ' . number_format(round($price , 2));

        return ' ' . number_format(round($price * $currencyValue, 2)) . ' ' . $currencySymbol;
    }

    public function getPriceRangeAttribute()
    {
        $lowest     = $this->getRawOriginal('lowest_price');
        $highest    = $this->getRawOriginal('highest_price');

        $currencies = Cache::rememberForever('currencies', function () {
            return Content::with('translations')->where('type','currencies')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC')->get();
        });

        $selectedCurrency = Cookie::get('default-currency') ?: "TRY";

        $selectedCurrency = $currencies->where('currency_code', $selectedCurrency)->first();

        if(empty($selectedCurrency))
        {
            return number_format((float) $lowest) . ' - ' . number_format((float) $highest);
        }

        return  number_format((float) $lowest * (float) $selectedCurrency->currency_value) . ' - ' . number_format((float) $highest * (float) $selectedCurrency->currency_value);
    }

    // public function getFullLowestPriceAttribute()
    // {
    //     return 'full_lowest_price';  
    // }
    // public function getFullHighestPriceAttribute()
    // {
    //     return 'full_highest_price';  
    // }

}
