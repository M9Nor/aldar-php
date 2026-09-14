<?php

return [
    'all'               => 'همه',
    'cities_areas'      => 'شهرها و مناطق', 
    'name_ar'           => 'اسم عربی', 
    'name_en'           => 'اسم انگلیسی',  
    'name_tr'           => 'اسم ترکی',  
    'text_ar'           => 'آدرس به عربی',  
    'text_en'           => 'آدرس به انگلیسی', 
    'text_tr'           => 'آدرس به ترکی', 
    'image_ar'          => 'عکس ها به عربی', 
    'image_en'          => 'عکس ها به انگلیسی', 
    'image_tr'          => 'عکس ها به ترکی',  
    'description_ar'    => 'توضیحات به عربی', 
    'description_en'    => 'توضیحات به انگلیسی', 
    'description_tr'    => 'توضیحات به ترکی', 
    'about_ar'          => 'درباره منطقه به عربی', 
    'about_en'          => 'درباره منطقه به انگلیسی', 
    'about_tr'          => 'درباره منطقه به ترکی', 
    'details_ar'        => 'جزییات به عربی',
    'details_en'        => 'جزییات به انگلیسی', 
    'details_tr'       => 'جزییات به ترکی', 
    'short_description_ar'    => 'خلاصه به عربی', 
    'short_description_en'    => 'خلاصه به انگلیسی', 
    'short_description_tr'    => 'خلاصه به ترکی', 
    'keywords'          => 'کلید واژه ها',  
    'city'              => 'شهر', 
    'cities'            => 'شهر ها',
    'country'           => 'کشور',
    'countries'         => 'کشور ها', 
    'title'             => 'منطقه',  
    'tags'              => 'برچسب ها',  
    'create_new_area'   => 'اضافه کردن یک منطقه جدید', 
    'create_new_country'=> 'اضافه کردن یک کشور جدید',
    'create_new_city'   => 'اضافه کردن یک شهر جدید',
    'edit_area'         => 'ویرایش منطقه', 
    'edit_country'      => 'ویرایش کشور',
    'edit_city'         => 'ویرایش شهر',
    'edit_tag'          => 'ویرایش برچسب ها', 
    'create_new_tag'    => 'اضافه کردن یک برچسب جدید', 
    'datatable'     => [
        'id'            => '#',
        'area'          => 'منطقه', 
        'country'       => 'کشور', 
        'city'          => 'شهر',
        'description'   => 'توضیحات',
        'actions'       => 'ابزار', 
        'tags'          => 'برچسب ها', 
    ],
    'fields' => [
        'slug'        => [
            'label'         => 'slag', 
            'placeholder'   => 'متن انگلیسی با حروف کوچک وارد کنید',
            'help'          => '',
        ],
        'link'        => [
            'label'         => 'لینک',  
            'placeholder'   => 'لینک را وارد کنید',  
            'help'          => '',
        ],
        'tags'        => [
            'label'         => 'برچسب ها', 
            'placeholder'   => 'برچسب ها را وارد کنید', 
            'help'          => '',
        ],
        'name' => [
            'label'         => 'اسم',  
            'placeholder'   => 'اسم را وارد کنید',  
            'help'          => '',
        ],
        'text' => [
            'label'         => 'اسم',  
            'placeholder'   => 'اسم را وارد کنید',  
            'help'          => '',
        ],
        'description' => [
            'label'         => 'توضیحات', 
            'placeholder'   => 'توضیحات را بنویسید',  
            'help'          => '',
        ],
        'about' => [
            'label'         => 'درباره منطقه', 
            'placeholder'   => 'ورود به در مورد منطقه',   
            'help'          => '',
        ],
        'short_description' => [
            'label'         => 'خلاصه', 
            'placeholder'   => 'ورود به خلاصه',   
            'help'          => '',
        ],
        'details' => [
            'label'         => 'جزییات',  
            'placeholder'   => 'ورود به جزئیات',
            'help'          => '',
        ],
        'keywords' => [
            'label'         => 'کلید واژه ها', 
            'placeholder'   => 'ورود به کلید واژه ها',
            'help'          => '',
        ],
        'image' => [
            'label'         => 'تصاویر', 
            'placeholder'   => 'ورود به تصاویر',  
            'help'          => '',
        ],
        'sort_order'    => [
            'label'         => 'سفارش',
            'placeholder'   => 'ورود به سفارش', 
            'help'          => '',
        ]
    ],
    'stats' => [
        'title' => 'آمار', 
    ],
    'custom_fields' => [
        'price_change' => [
            'title'         => 'داده های قیمت منطقه', 
            'rent' => [
                'title'     => ' تغییرات قیمت اجاره واحدهای مسکونی در منطقه', 
                'last_year' => [
                    'label'         => 'سال گذشته', 
                    'placeholder'   => 'درصد از 0 تا 100' ,
                    'help'          => '',
                ],
                'last_3_years' => [
                    'label'         => 'سه سال گذشته',
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                'last_5_years' => [
                    'label'         => 'پنج سال گذشته', 
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
            ],
            'sale' => [
                'title'     => 'تغییرات قیمت اجاره واحدهای مسکونی در منطقه', 
                'last_year' => [
                    'label'         => 'سال گذشته',  
                    'placeholder'   => 'درصد از 0 تا 100', 
                    'help'          => '',
                ],
                'last_3_years' => [
                    'label'         => 'سه سال گذشته', 
                    'placeholder'   => 'درصد از 0 تا 100', 
                    'help'          => '',
                ],
                'last_5_years' => [
                    'label'         => 'پنج سال گذشته',   
                    'placeholder'   => 'درصد از 0 تا 100', 
                    'help'          => '',
                ],
            ],
        ],
        'demographic_data' => [
            'title'        => 'اطلاعات جمعیتی منطقه',
            'people_data' => [
                'title'     => ' داده های جمعیت منطقه', 
                'growth_rate' => [
                    'label'        => 'نرخ افزایش سالانه در منطقه', 
                    'placeholder'   => 'درصد از 0 تا 100', 
                    'help'          => '',
                    'year'         => 'سال',  
                ],
                'total_population' => [
                    'thousand'         => 'هزاران مردم', 
                    'label'         => 'کل جمعیت در منطقه', 
                    'placeholder'   => '',
                    'help'          => '',
                ],
                'ecomomic_and_social_evaluation' => [
                    'label'         => 'ارزیابی وضعیت اقتصادی در منطقه', 
                    'placeholder'   => '',
                    'help'          => '',
                ],
            ],
            'education_status' => [
                'title'     => 'وضعیت آموزشی در منطقه', 
                'uneducated' => [
                    'label'         => 'تحصیل نکرده',  
                    'placeholder'   => 'درصد از 0 تا 100', 

                    'help'          => '',
                ],
                'primary' => [
                    'label'         => 'ابتدایی',  
                    'placeholder'   => 'درصد از 0 تا 100', 

                    'help'          => '',
                ],
                'elementary' => [
                    'label'         => 'متوسط',  
                    'placeholder'   => 'درصد از 0 تا 100',

                    'help'          => '',
                ],
                'secondary' => [
                    'label'         => 'ثانوی',
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                'university' => [
                    'label'         => 'دانشگاهی',  
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
            ],
            'age_distribution' => [
                'title'     => 'توزیع سن در منطقه',  
                '0_to_14' => [
                    'label'         => 'از صفر تا 14 سال',
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                '15_to_24' => [
                    'label'         => 'از 15 تا 24 سال',  
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                '25_to_34' => [
                    'label'         => 'از 25 تا 34 سال',  
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                '35_to_44' => [
                    'label'         => ' از 35 تا 44 سال',  
                    'placeholder'   => 'از 35 تا 44 سال',
                    'help'          => '',
                ],
                '45_to_54' => [
                    'label'         => 'از 45 تا 54 سال',  
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                '55_to_64' => [
                    'label'         => 'از 55 تا 64 سال',  
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                'over_65' => [
                    'label'         => 'بزرگتر از 65 سال',  
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
            ],
            'marital_condition' => [
                'title'     => 'وضعیت اجتماعی در منطقه',  
                'single' => [
                    'label'         => 'مجرد', 
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                'married' => [
                    'label'         => 'متاهل', 
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                'divorced' => [
                    'label'         => 'مطلقه',  
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
                'widow' => [
                    'label'         => 'بیوه',  
                    'placeholder'   => 'درصد از 0 تا 100',
                    'help'          => '',
                ],
            ],
        ],
    ],
    'sections' => [
        'previous' => 'قبل', 
        'submit' => 'ذخیره کردن', 
        'next_step' => 'بعدی', 
        'profile' => [
            'title'         => 'مشخصات',
            'description'   => 'اطلاعات شخصی کاربر',
        ],
        'account' => [
            'title'         => 'حساب',
            'description'   => 'اطلاعات شخصی کاربر', 
        ],
        'address' => [
            'title'         => 'آدرس',  
            'description'   => 'جزئیات آدرس کاربر',
        ],
        'review' => [
            'title'         => 'ذخیره کردن',  
            'description'   => 'مشاهده و ذخیره اطلاعات',
        ],
    ],
    'tags'  => [
        'tags'  => 'برچسب ها',  
        'fields' => [

            'description' => [
                'label'         => 'توضیحات',  
                'placeholder'   => 'ورود به توضیحات',  
                'help'          => '',
            ],
            'language' => [
                'label'         => 'زبان',  
                'placeholder'   => 'ورود به زبان',   
                'help'          => 'زبانی را که می خواهید محتوا به آن ترجمه شود انتخاب کنید', 
            ],
            'image' => [
                'label'         => 'تصویر', 
                'placeholder'   => 'درج تصویر',
                'help'          => '',
            ],
            'text' => [
                'label'         => 'آدرس', 
                'placeholder'   => 'ورود به آدرس',  
                'help'          => '',
            ],
            'keywords' => [
                'label'         => 'کلید واژه ها',  
                'placeholder'   => 'ورود به کلید واژه ها',  
                'help'          => '',
            ],
        ]
    ]
];
