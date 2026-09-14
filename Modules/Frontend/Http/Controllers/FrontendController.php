<?php

namespace Modules\Frontend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Modules\Cms\Entities\Content;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\Config;
use Modules\Backend\Entities\Project;
use Modules\Cms\Entities\FlagCountry;

class FrontendController extends Controller
{
    public $data = [];
    public $locale;
    protected $apiKey = 'f0e0c4ba069d43af6200';
    
    public function __construct()
    {
        
        $this->data['currentLang'] = \LaravelLocalization::getCurrentLocale();
        $this->data['supportedLangs'] = \LaravelLocalization::getLocalesOrder();
        $this->data['currentLangName'] = \LaravelLocalization::getCurrentLocaleName();
        $this->data['currentLangNative'] = \LaravelLocalization::getCurrentLocaleNative();
        $this->data['langDirection'] = \LaravelLocalization::getCurrentLocaleDirection();
        $this->data['all_countries']            = FlagCountry::where('code','!=','')->where('status','yes')->orderBy('order_by','DESC')->get();
        $this->locale = app()->getLocale();

        $this->data['contents'] = Config::with('translations')->get()->keyBy('key');

        foreach ($this->data['contents'] as $key => $setting) {
            if(!empty($setting->translateOrFirst()->description))
            {
                $this->data['contents'][$key]->value = $setting->translateOrFirst()->description;
            }
            else
            {
                $this->data['contents'][$key]->value = $setting->val;
            }
        }
        
    }

    public function setCurrency(Request $request)
    {
        $curr = $request->currency;
        Cookie::queue('default-currency', $curr);
        return redirect()->back();

    }
    public function cookies()
    {
        Cookie::queue('cookies', 'yes', 260000);
        return response()->json([
            'success' => true
        ]);
    }

    public static  function currencyApi($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              "Cookie: __cfduid=dc9183c00a4e16af2c6c6295d42534d651595493284"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function getCurrencyPrice($currency, $defaultCurrency = 'TRY')
    {
        $url = "https://free.currconv.com/api/v7/convert?q=" . $defaultCurrency . "_" . $currency . "&compact=ultra&apiKey=" . $this->apiKey;
        return json_decode(self::currencyApi($url), true)[$defaultCurrency . "_" . $currency];
    }

    public  function storeCurrencies() {
        $url = 'https://free.currconv.com/api/v7/currencies?apiKey=' . $this->apiKey;
        $websiteCurrencies = Content::where('type', 'currencies')->where('slug', '!=', null)->get();
        $currencies = json_decode(self::currencyApi($url), true);
        try {
            \DB::transaction(function() use ($websiteCurrencies, $currencies) {
                // dd(self::getCurrencyPrice(''));

                foreach ($websiteCurrencies as $key => $currency) {
                    if(array_key_exists($currency->currency_code, $currencies['results']))
                    {
                        $currency->currency_value = $this->getCurrencyPrice($currency->currency_code);
                        $currency->save();
                    }
                }
            });
        } catch (\Exception $e) {
            dd($e);
        }

    }
}
