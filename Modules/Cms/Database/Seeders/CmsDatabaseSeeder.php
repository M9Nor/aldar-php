<?php

namespace Modules\Cms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\Area;
use Modules\Cms\Entities\Country;

class CmsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UsersTableSeeder::class);
        // $this->seedCountriesAndCities();
        // $this->call(CountriesTableSeeder::class);
        // $this->call(CitiesTableSeeder::class);
    }
    protected function seedCountriesAndCities()
    {
        $Country                       = Country::whereTranslation('name', 'تركيا')->firstOrNew([
            'id' => '1'
        ]);

        $Country->{'name:ar'}         = 'تركيا';
        $Country->{'name:en'}         = 'Turkey';
        $Country->save();

        $City                       = City::whereTranslation('name', 'Istanbul')->firstOrNew([
            'native_name' => 'Istanbul'
        ]);
         $City->country_id =  $Country->id;

        $City->{'name:ar'}         = 'اسطنبول';
        $City->{'name:en'}         = 'Istanbul';
        $City->save();

        $City                       = City::whereTranslation('name', 'Turkey')->firstOrNew([
            'native_name' => 'Turkey'
        ]);
         $City->country_id =  $Country->id;

        $City->{'name:ar'}         = 'تركيا';
        $City->{'name:en'}         = 'Turkey';
        $City->save();

        $Area                       = Area::whereTranslation('name', 'بيليك دوزو')->firstOrNew([
            'native_name' => 'Beylikduzu'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بيليك دوزو';
        $Area->{'name:en'}         = 'Beylikduzu';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'باشاك شهير')->firstOrNew([
            'native_name' => 'Basaksehir'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'باشاك شهير';
        $Area->{'name:en'}         = 'Basaksehir';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'بهجة شهير')->firstOrNew([
            'native_name' => 'bahcesehir'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بهجة شهير';
        $Area->{'name:en'}         = 'bahcesehir';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'اسبارطه كوله')->firstOrNew([
            'native_name' => 'ispartakule'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'اسبارطه كوله';
        $Area->{'name:en'}         = 'ispartakule';
        $Area->save();
        $Area                       = Area::whereTranslation('name', ' بكر كوي')->firstOrNew([
            'native_name' => 'Bakirkoy'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = ' بكر كوي';
        $Area->{'name:en'}         = 'Bakirkoy';
        $Area->save();
        $Area                       = Area::whereTranslation('name', '   باسين اكسبريس')->firstOrNew([
            'native_name' => 'basin express'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = '   باسين اكسبريس';
        $Area->{'name:en'}         = 'basin express';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'زيتون بورنو')->firstOrNew([
            'native_name' => 'Zeytunburnu'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'زيتون بورنو';
        $Area->{'name:en'}         = 'Zeytunburnu';
        $Area->save();
        $Area                       = Area::whereTranslation('name', ' شيرين ايفلر')->firstOrNew([
            'native_name' => 'sirinevler'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = ' شيرين ايفلر';
        $Area->{'name:en'}         = 'sirinevler';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'كايت هانه')->firstOrNew([
            'native_name' => 'Bahcelievler'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'كايت هانه';
        $Area->{'name:en'}         = 'Bahcelievler';
        $Area->save();
        $Area                       = Area::whereTranslation('name', ' اسنيورت')->firstOrNew([
            'native_name' => 'Esenyurt'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = ' اسنيورت';
        $Area->{'name:en'}         = 'Esenyurt';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'غازي عثمان باشا')->firstOrNew([
            'native_name' => 'Gaziosmanpasa'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'غازي عثمان باشا';
        $Area->{'name:en'}         = 'Gaziosmanpasa';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'كوتشوك جكمجة')->firstOrNew([
            'native_name' => 'Kucukcekmece'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'كوتشوك جكمجة';
        $Area->{'name:en'}         = 'Kucukcekmece';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'بيوك جكمجة')->firstOrNew([
            'native_name' => 'Buyukcekmce'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بيوك جكمجة';
        $Area->{'name:en'}         = 'Buyukcekmce';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'بيرم باشا')->firstOrNew([
            'native_name' => 'Buyukcekmce'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بيرم باشا';
        $Area->{'name:en'}         = 'Buyukcekmce';
        $Area->save();
        $Area                       = Area::whereTranslation('name', ' أفجلار')->firstOrNew([
            'native_name' => 'Avcilar'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = ' أفجلار';
        $Area->{'name:en'}         = 'Avcilar';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'بهجلي ايفلر')->firstOrNew([
            'native_name' => 'Bahcelieveler'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بهجلي ايفلر';
        $Area->{'name:en'}         = 'Bahcelieveler';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'شيشلي')->firstOrNew([
            'native_name' => 'Sisli'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'شيشلي';
        $Area->{'name:en'}         = 'Sisli';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'الفاتح')->firstOrNew([
            'native_name' => 'Fatih'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'الفاتح';
        $Area->{'name:en'}         = 'Fatih';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'أيوب')->firstOrNew([
            'native_name' => 'Eyup'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'أيوب';
        $Area->{'name:en'}         = 'Eyup';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'تقسيم')->firstOrNew([
            'native_name' => 'taksim '
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'تقسيم';
        $Area->{'name:en'}         = 'taksim ';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'سلطان غازي')->firstOrNew([
            'native_name' => 'Sultangazi '
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'سلطان غازي';
        $Area->{'name:en'}         = 'Sultangazi ';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'ساريير')->firstOrNew([
            'native_name' => 'Sarıyer'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'ساريير';
        $Area->{'name:en'}         = 'Sarıyer';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'باغجلار')->firstOrNew([
            'native_name' => 'Bagcilar'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'باغجلار';
        $Area->{'name:en'}         = 'Bagcilar';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'بشكطاش')->firstOrNew([
            'native_name' => 'Besiktas'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بشكطاش';
        $Area->{'name:en'}         = 'Besiktas';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'بيه اوغلو')->firstOrNew([
            'native_name' => 'Beyoglu'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بيه اوغلو';
        $Area->{'name:en'}         = 'Beyoglu';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'غونغوران')->firstOrNew([
            'native_name' => 'Gungoren'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'غونغوران';
        $Area->{'name:en'}         = 'Gungoren';
        $Area->save();
        $Area                       = Area::whereTranslation('name', '')->firstOrNew([
            'native_name' => ' '
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = '';
        $Area->{'name:en'}         = '';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'أرناؤوط كوي')->firstOrNew([
            'native_name' => 'Arnavutkoy'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'أرناؤوط كوي';
        $Area->{'name:en'}         = 'Arnavutkoy';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'تشاتلجا')->firstOrNew([
            'native_name' => 'catalca '
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'تشاتلجا';
        $Area->{'name:en'}         = 'catalca';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'سيليفري')->firstOrNew([
            'native_name' => 'Silvery'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'سيليفري';
        $Area->{'name:en'}         = 'Silvery';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'أوسكودار')->firstOrNew([
            'native_name' => 'Uskudar'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'أوسكودار';
        $Area->{'name:en'}         = 'Uskudar';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'عمرانية')->firstOrNew([
            'native_name' => 'umraniye'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'عمرانية';
        $Area->{'name:en'}         = 'umraniye';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'أتا شهير')->firstOrNew([
            'native_name' => 'Atasehir'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'أتا شهير';
        $Area->{'name:en'}         = 'Atasehir';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'بنديك')->firstOrNew([
            'native_name' => ' Pendik'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بنديك';
        $Area->{'name:en'}         = 'Pendik';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'تشكمه كوي')->firstOrNew([
            'native_name' => ' Cekmekoy'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'تشكمه كوي';
        $Area->{'name:en'}         = 'Cekmekoy';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'توزلا')->firstOrNew([
            'native_name' => 'Tuzla'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'توزلا';
        $Area->{'name:en'}         = 'Tuzla';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'كادي كوي')->firstOrNew([
            'native_name' => 'Kadikoy '
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'كادي كوي';
        $Area->{'name:en'}         = 'Kadikoy';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'ادالار - جزر الأميرات')->firstOrNew([
            'native_name' => 'Adalar'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'ادالار - جزر الأميرات';
        $Area->{'name:en'}         = 'Adalar';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'سلطان بيلي')->firstOrNew([
            'native_name' => 'Sultanbeyli '
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'سلطان بيلي';
        $Area->{'name:en'}         = 'Sultanbeyli';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'شيلا')->firstOrNew([
            'native_name' => ' Sile'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'شيلا';
        $Area->{'name:en'}         = 'Sile';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'سنجق تبه')->firstOrNew([
            'native_name' => 'Sancaktepe '
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'سنجق تبه';
        $Area->{'name:en'}         = 'Sancaktepe';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'كارتال')->firstOrNew([
            'native_name' => 'Kartal'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'كارتال';
        $Area->{'name:en'}         = 'Kartal';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'مالتبه')->firstOrNew([
            'native_name' => 'Maltepe '
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'مالتبه';
        $Area->{'name:en'}         = 'Maltepe';
        $Area->save();
        $Area                       = Area::whereTranslation('name', 'بيكوز')->firstOrNew([
            'native_name' => 'Beykoz'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'بيكوز';
        $Area->{'name:en'}         = 'Beykoz';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'الجانب الاوروبي')->firstOrNew([
            'native_name' => 'European Side'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'الجانب الاوروبي';
        $Area->{'name:en'}         = 'European Side';
        $Area->save();

        $Area                       = Area::whereTranslation('name', 'الجانب الاسيوي')->firstOrNew([
            'native_name' => 'Asian Side'
        ]);
         $Area->city_id =  $City->id;

        $Area->{'name:ar'}         = 'الجانب الاسيوي';
        $Area->{'name:en'}         = 'Asian Side';
        $Area->save();


        ////////////////////////////
        $City                       = City::whereTranslation('name', 'Bodrum')->firstOrNew([
            'native_name' => 'Bodrum'
        ]);
         $City->country_id =  $Country->id;
        $City->{'name:ar'}         = 'بودروم';
        $City->{'name:en'}         = 'Bodrum';
        $City->save();


        $City                       = City::whereTranslation('name', 'Yalova')->firstOrNew([
            'native_name' => 'Yalova'
        ]);
         $City->country_id =  $Country->id;
        $City->{'name:ar'}         = 'يلوا';
        $City->{'name:en'}         = 'Yalova';
        $City->save();


        $City                       = City::whereTranslation('name', 'bursa')->firstOrNew([
            'native_name' => 'bursa'
        ]);
         $City->country_id =  $Country->id;
        $City->{'name:ar'}         = 'بورصة';
        $City->{'name:en'}         = 'bursa';
        $City->save();



        $City                       = City::whereTranslation('name', 'Sakarya')->firstOrNew([
            'native_name' => 'Sakarya'
        ]);
         $City->country_id =  $Country->id;
        $City->{'name:ar'}         = 'سكاريا';
        $City->{'name:en'}         = 'Sakarya';
        $City->save();

        $City                       = City::whereTranslation('name', 'kocaeli')->firstOrNew([
            'native_name' => 'kocaeli'
        ]);
         $City->country_id =  $Country->id;
        $City->{'name:ar'}         = 'كوجالي';
        $City->{'name:en'}         = 'kocaeli';
        $City->save();

        $City                       = City::whereTranslation('name', 'Trabzon')->firstOrNew([
            'native_name' => 'Trabzon'
        ]);
         $City->country_id =  $Country->id;
        $City->{'name:ar'}         = 'طرابزون';
        $City->{'name:en'}         = 'Trabzon';
        $City->save();

        $City                       = City::whereTranslation('name', 'Antalya')->firstOrNew([
            'native_name' => 'Antalya'
        ]);
         $City->country_id =  $Country->id;
        $City->{'name:ar'}         = 'أنطاليا';
        $City->{'name:en'}         = 'Antalya';
        $City->save();
    }

}
