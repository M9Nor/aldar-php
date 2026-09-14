<?php

return [
    'all'               => 'الكل',
    'cities_areas'      => 'المدن والمناطق',
    'name_ar'           => 'الاسم باللغة العربية',
    'name_en'           => 'الاسم باللغة الإنكليزية',
    'name_tr'           => 'الاسم باللغة التركية',
    'text_ar'           => 'العنوان باللغة العربية',
    'text_en'           => 'العنوان باللغة الإنكليزية',
    'text_tr'           => 'العنوان باللغة التركية',
    'image_ar'          => 'الصورة باللغة العربية',
    'image_en'          => 'الصورة باللغة الإنكليزية',
    'image_tr'          => 'الصورة باللغة التركية',
    'description_ar'    => 'الشرح باللغة العربية',
    'description_en'    => 'الشرح باللغة الإنكليزية',
    'description_tr'    => 'الشرح باللغة التركية',
    'about_ar'          => 'النبذة عن المنطقة باللغة العربية',
    'about_en'          => 'النبذة عن المنطقة باللغة الإنكليزية',
    'about_tr'          => 'النبذة عن المنطقة باللغة التركية',
    'details_ar'        => 'التفاصيل باللغة العربية',
    'details_en'        => 'التفاصيل باللغة الإنكليزية',
    'details_tr'        => 'التفاصيل باللغة التركية',
    'short_description_ar'    => 'الشرح المختصر باللغة العربية',
    'short_description_en'    => 'الشرح المختصر باللغة الإنكليزية',
    'short_description_tr'    => 'الشرح المختصر باللغة التركية',
    'keywords'          => 'الكلمات المفتاحية',
    'city'              => 'المدينة',
    'cities'            => 'المدن',
    'country'           => 'الدولة',
    'countries'         => 'الدول',
    'title'             => 'المناطق',
    'tags'              => 'الوسوم',
    'create_new_area'   => 'إضافة منطقة جديدة',
    'create_new_country'=> 'إضافة دولة جديدة',
    'create_new_city'   => 'إضافة مدينة جديدة',
    'edit_area'         => 'تعديل منطقة ',
    'edit_country'      => 'تعديل الدولة',
    'edit_city'         => 'تعديل المدينة',
    'edit_tag'          => 'تعديل وسم ',
    'create_new_tag'    => 'إضافة وسم جديد',
    'datatable'     => [
        'id'            => '#',
        'area'          => 'المنطقة',
        'country'       => 'الدولة',
        'city'          => 'المدينة',
        'description'   => 'الشرح',
        'actions'       => 'الأدوات',
        'tags'          => 'الوسم',
    ],
    'fields' => [
        'slug'        => [
            'label'         => 'السلاغ',
            'placeholder'   => 'ادخل نص انكليزي بحروف صغيرة فقط',
            'help'          => '',
        ],
        'link'        => [
            'label'         => 'الرابط',
            'placeholder'   => 'ادخل الرابط',
            'help'          => '',
        ],
        'tags'        => [
            'label'         => 'الوسوم',
            'placeholder'   => 'ادخل الوسوم',
            'help'          => '',
        ],
        'name' => [
            'label'         => 'الاسم',
            'placeholder'   => 'ادخل الاسم',
            'help'          => '',
        ],
        'text' => [
            'label'         => 'الاسم',
            'placeholder'   => 'ادخل الاسم',
            'help'          => '',
        ],
        'description' => [
            'label'         => 'الشرح',
            'placeholder'   => 'ادخل الشرح',
            'help'          => '',
        ],
        'about' => [
            'label'         => 'نبذة عن المنطقة',
            'placeholder'   => 'ادخل نبذة عن المنطقة',
            'help'          => '',
        ],
        'short_description' => [
            'label'         => 'شرح مختصر',
            'placeholder'   => 'ادخل شرح مختصر',
            'help'          => '',
        ],
        'details' => [
            'label'         => 'تفاصيل',
            'placeholder'   => 'ادخل تفاصيل',
            'help'          => '',
        ],
        'keywords' => [
            'label'         => 'كلمات مفتاحية',
            'placeholder'   => 'ادخل كلمات مفتاحية',
            'help'          => '',
        ],
        'image' => [
            'label'         => 'الصورة',
            'placeholder'   => 'ادخل الصورة',
            'help'          => '',
        ],
        'sort_order'    => [
            'label'         => 'الترتيب',
            'placeholder'   => 'ادخل الترتيب',
            'help'          => '',
        ]
    ],
    'stats' => [
        'title' => 'الإحصائيات',
    ],
    'custom_fields' => [
        'price_change' => [
            'title'         => 'بيانات الأسعار بالمنطقة',
            'rent' => [
                'title'     => 'التغير في أسعار إيجار الوحدات السكنية بالمنطقة',
                'last_year' => [
                    'label'         => 'آخر سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'last_3_years' => [
                    'label'         => 'آخر 3 سنوات',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'last_5_years' => [
                    'label'         => 'آخر 5 سنوات',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
            ],
            'sale' => [
                'title'     => 'التغير في أسعار بيع الوحدات السكنية بالمنطقة',
                'last_year' => [
                    'label'         => 'آخر سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'last_3_years' => [
                    'label'         => 'آخر 3 سنوات',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'last_5_years' => [
                    'label'         => 'آخر 5 سنوات',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
            ],
        ],
        'demographic_data' => [
            'title'         => 'البيانات الديموغرافية بالمنطقة',
            'people_data' => [
                'title'     => 'بيانات السكان بالمنطقة',
                'growth_rate' => [
                    'label'         => 'نسبة الزيادة السنوية بالمنطقة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                    'year'         => 'سنة',
                ],
                'total_population' => [
                    'thousand'         => 'ألف نسمة',
                    'label'         => 'إجمالي عدد السكان بالمنطقة',
                    'placeholder'   => '',
                    'help'          => '',
                ],
                'ecomomic_and_social_evaluation' => [
                    'label'         => 'تقييم الحالة الاقتصادية والاجتماعية بالمنطقة',
                    'placeholder'   => '',
                    'help'          => '',
                ],
            ],
            'education_status' => [
                'title'     => 'الحالة التعليمية بالمنطقة',
                'uneducated' => [
                    'label'         => 'غير متعلم',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'primary' => [
                    'label'         => 'ابتدائي',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'elementary' => [
                    'label'         => 'متوسط إعدادي',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'secondary' => [
                    'label'         => 'ثانوي',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'university' => [
                    'label'         => 'جامعي',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
            ],
            'age_distribution' => [
                'title'     => 'التوزيع العمري بالمنطقة',
                '0_to_14' => [
                    'label'         => 'من 0 إلى 14 سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                '15_to_24' => [
                    'label'         => 'من 15 إلى 24 سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                '25_to_34' => [
                    'label'         => 'من 25 إلى 34 سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                '35_to_44' => [
                    'label'         => 'من 35 إلى 44 سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                '45_to_54' => [
                    'label'         => 'من 45 إلى 54 سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                '55_to_64' => [
                    'label'         => 'من 55 إلى 64 سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'over_65' => [
                    'label'         => 'أكبر من 65 سنة',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
            ],
            'marital_condition' => [
                'title'     => 'الحالة الاجتماعية بالمنطقة',
                'single' => [
                    'label'         => 'أعزب',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'married' => [
                    'label'         => 'متزوج',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'divorced' => [
                    'label'         => 'مطلق',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
                'widow' => [
                    'label'         => 'أرمل',
                    'placeholder'   => 'نسبة مئوية (بين 0 و100)',
                    'help'          => '',
                ],
            ],
        ],
    ],
    'sections' => [
        'previous' => 'السابق',
        'submit' => 'حفظ',
        'next_step' => 'التالي',
        'profile' => [
            'title'         => 'الملف الشخصي',
            'description'   => 'معلومات المستخدم الشخصية',
        ],
        'account' => [
            'title'         => 'الحساب',
            'description'   => 'معلومات حساب المستخدم',
        ],
        'address' => [
            'title'         => 'العنوان',
            'description'   => 'تفاصيل عنوان المستخدم',
        ],
        'review' => [
            'title'         => 'الحفظ',
            'description'   => 'استعراض المعلومات وحفظها',
        ],
    ],
    'tags'  => [
        'tags'  => 'الوسوم',
        'fields' => [

            'description' => [
                'label'         => 'الشرح',
                'placeholder'   => 'ادخل الشرح',
                'help'          => '',
            ],
            'language' => [
                'label'         => 'اللغة',
                'placeholder'   => 'ادخل اللغة',
                'help'          => 'قم بتحديد اللغة التي ترغب في ترجمة المحتويات إليها ',
            ],
            'image' => [
                'label'         => 'الصورة',
                'placeholder'   => 'ادخل الصورة',
                'help'          => '',
            ],
            'text' => [
                'label'         => 'العنوان',
                'placeholder'   => 'ادخل العنوان',
                'help'          => '',
            ],
            'keywords' => [
                'label'         => 'الكلمات المفتاحية',
                'placeholder'   => 'ادخل الكلمات المفتاحية',
                'help'          => '',
            ],
        ]
    ]
];
