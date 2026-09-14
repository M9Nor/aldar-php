<?php

return [
    'all'               => 'All',
    'cities_areas'      => 'Cities and regions',
    'name_ar'           => 'Arabic name',
    'name_en'           => 'English name',
    'name_tr'           => 'Turkish name',
    'text_ar'           => 'Address in Arabic',
    'text_en'           => 'Address in English',
    'text_tr'           => 'Address in Turkish',
    'image_ar'          => 'Photos in Arabic',
    'image_en'          => 'Photos in English',
    'image_tr'          => 'Photos in Turkish',
    'description_ar'    => 'Explanation in Arabic',
    'description_en'    => 'Explanation in English',
    'description_tr'    => 'Explanation in Turkish',
    'about_ar'          => 'About the region in Arabic',
    'about_en'          => 'About the region in English',
    'about_tr'          => 'About the region in Turkish',
    'details_ar'        => 'Details in Arabic',
    'details_en'        => 'Details in English',
    'details_tr'       => 'Details in Turkish',
    'short_description_ar'    => 'Brief in Arabic',
    'short_description_en'    => 'Brief in English',
    'short_description_tr'    => 'Brief in Turkish',
    'keywords'          => 'Keywords',
    'city'              => 'City',
    'cities'            => 'Cities',
    'country'           => 'Country',
    'countries'         => 'Countries',
    'title'             => 'Region',
    'tags'              => 'Tags',
    'create_new_area'   => 'Add a new region',
    'create_new_country'=> 'Add a new country',
    'create_new_city'   => 'Add a new city',
    'edit_area'         => 'Region edit',
    'edit_country'      => 'Country edit',
    'edit_city'         => 'City edit',
    'edit_tag'          => 'Tag edit',
    'create_new_tag'    => 'Add a new tag',
    'datatable'     => [
        'id'            => '#',
        'area'          => 'Region',
        'country'       => 'Country',
        'city'          => 'City',
        'description'   => 'Explanation',
        'actions'       => 'Tools',
        'tags'          => 'Tags',
    ],
    'fields' => [
        'slug'        => [
            'label'         => 'Slag',
            'placeholder'   => 'Enter English text in small letters',
            'help'          => '',
        ],
        'link'        => [
            'label'         => 'Link',
            'placeholder'   => 'Insert the link',
            'help'          => '',
        ],
        'tags'        => [
            'label'         => 'Tags',
            'placeholder'   => 'Enter tags',
            'help'          => '',
        ],
        'name' => [
            'label'         => 'Name',
            'placeholder'   => 'Enter the name',
            'help'          => '',
        ],
        'text' => [
            'label'         => 'Name',
            'placeholder'   => 'Enter the name',
            'help'          => '',
        ],
        'description' => [
            'label'         => 'Explanation',
            'placeholder'   => 'Write explanation',
            'help'          => '',
        ],
        'about' => [
            'label'         => 'About the region',
            'placeholder'   => 'Enter about the region',
            'help'          => '',
        ],
        'short_description' => [
            'label'         => 'Brief',
            'placeholder'   => 'Enter brief',
            'help'          => '',
        ],
        'details' => [
            'label'         => 'Details',
            'placeholder'   => 'Enter details',
            'help'          => '',
        ],
        'keywords' => [
            'label'         => 'Keywords',
            'placeholder'   => 'Enter Keywords',
            'help'          => '',
        ],
        'image' => [
            'label'         => 'Images',
            'placeholder'   => 'Insert the image',
            'help'          => '',
        ],
        'sort_order'    => [
            'label'         => 'Order',
            'placeholder'   => 'Enter the order',
            'help'          => '',
        ]
    ],
    'stats' => [
        'title' => 'Statistics',
    ],
    'custom_fields' => [
        'price_change' => [
            'title'         => 'Region price data',
            'rent' => [
                'title'     => ' Rental price changes of residential units in the region',
                'last_year' => [
                    'label'         => 'The last year',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'last_3_years' => [
                    'label'         => 'Last 3 years',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'last_5_years' => [
                    'label'         => 'Last 5 years',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
            ],
            'sale' => [
                'title'     => 'Rental price changes of residential units in the region',
                'last_year' => [
                    'label'         => 'Last year',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'last_3_years' => [
                    'label'         => 'Last 3 years',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'last_5_years' => [
                    'label'         => 'Last 5 years',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
            ],
        ],
        'demographic_data' => [
            'title'        => 'Demographic data of the region',
            'people_data' => [
                'title'     => ' Population data of the region',
                'growth_rate' => [
                    'label'        => 'Annual increase rate in the region',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                    'year'         => 'Year',
                ],
                'total_population' => [
                    'thousand'         => 'Thousand people',
                    'label'         => 'Total population in the region',
                    'placeholder'   => '',
                    'help'          => '',
                ],
                'ecomomic_and_social_evaluation' => [
                    'label'         => 'Socioeconomic status evaluation in the region',
                    'placeholder'   => '',
                    'help'          => '',
                ],
            ],
            'education_status' => [
                'title'     => 'Educational status in the region',
                'uneducated' => [
                    'label'         => 'Uneducated',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'primary' => [
                    'label'         => 'Elementary',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'elementary' => [
                    'label'         => 'Intermediate',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'secondary' => [
                    'label'         => 'Secondary',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'university' => [
                    'label'         => 'Academic',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
            ],
            'age_distribution' => [
                'title'     => 'Age-distribution in the region',
                '0_to_14' => [
                    'label'         => 'From 0 to 14 years old',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                '15_to_24' => [
                    'label'         => 'From 15 to 24 years old',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                '25_to_34' => [
                    'label'         => 'From 25 to 34 years old',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                '35_to_44' => [
                    'label'         => 'From 35 to 44 years old',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                '45_to_54' => [
                    'label'         => 'From 45 to 54 years old',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                '55_to_64' => [
                    'label'         => 'From 55 to 64 years old',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'over_65' => [
                    'label'         => 'Older than 65 years old',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
            ],
            'marital_condition' => [
                'title'     => 'Social status in the region',
                'single' => [
                    'label'         => 'Single',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'married' => [
                    'label'         => 'Married',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'divorced' => [
                    'label'         => 'Divorced',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
                'widow' => [
                    'label'         => 'Widow',
                    'placeholder'   => 'Percentage (of 0 to 100)',
                    'help'          => '',
                ],
            ],
        ],
    ],
    'sections' => [
        'previous' => 'Previous',
        'submit' => 'Save',
        'next_step' => 'Next',
        'profile' => [
            'title'         => 'Profile',
            'description'   => 'User personal information',
        ],
        'account' => [
            'title'         => 'Account',
            'description'   => 'User account information',
        ],
        'address' => [
            'title'         => 'Address',
            'description'   => 'User address details',
        ],
        'review' => [
            'title'         => 'Save',
            'description'   => 'View and save information',
        ],
    ],
    'tags'  => [
        'tags'  => 'Tags',
        'fields' => [

            'description' => [
                'label'         => 'Explanation',
                'placeholder'   => 'Enter the explanation',
                'help'          => '',
            ],
            'language' => [
                'label'         => 'Language',
                'placeholder'   => 'Enter language', 
                'help'          => 'Select the language you want contents be translated into',
            ],
            'image' => [
                'label'         => 'Image',
                'placeholder'   => 'Insert image',
                'help'          => '',
            ],
            'text' => [
                'label'         => 'Address',
                'placeholder'   => 'Enter Address',
                'help'          => '',
            ],
            'keywords' => [
                'label'         => 'Keywords',
                'placeholder'   => 'Enter Keywords',
                'help'          => '',
            ],
        ]
    ]

];
